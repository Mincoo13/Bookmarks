<?php

namespace Tests\Feature;

use App\Comment;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CommentTest extends TestCase
{
    use WithFaker;
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExample()
    {
        $this->assertTrue(true);
    }

    public function testNoToken(){
        $response = $this->json('POST', '/api/login', ['email' => 'sally.smith@example.com', 'password' => 'Sally123!']);
        $response->assertStatus(200);
        $response = $this->json('POST', '/api/comments', ['bookmark_id' => 1, 'text' => 'text']);
        $response->assertStatus(401);
        $response = $this->json('PATCH', '/api/comments/1', ['text' => 'text']);
        $response->assertStatus(401);
        $response = $this->json('DELETE', '/api/comments/1');
        $response->assertStatus(401);
    }

    public function testValidCreateComment(){
        $text = $this->faker->text;
        $email = 'sally.smith@example.com';
        $password = 'Sally123!';
        $response = $this->json('POST', '/api/login', ['email' => $email, 'password' => $password]);
        $response->assertStatus(200);
        $token = $response->json()['data']['token'];

//        Komentovanie vlastnej zalozky
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer' . $token,
        ])->json('POST', '/api/comments', ['bookmark_id' => 1, 'text' => $text]);
        $response->assertStatus(200);

//        Komentovanie cudzej zalozky
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer' . $token,
        ])->json('POST', '/api/comments', ['bookmark_id' => 5, 'text' => $text]);
        $response->assertStatus(200);
    }

    public function testInvalidCreateComment(){
        $text = $this->faker->text;
        $email = 'sally.smith@example.com';
        $password = 'Sally123!';
        $response = $this->json('POST', '/api/login', ['email' => $email, 'password' => $password]);
        $response->assertStatus(200);
        $token = $response->json()['data']['token'];

//        Pokus o pridanie komentaru bez textu
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer' . $token,
        ])->json('POST', '/api/comments', ['bookmark_id' => 1]);
        $response->assertStatus(409);

//        Pokus o komentovanie neverejnej zalozky
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer' . $token,
        ])->json('POST', '/api/comments', ['bookmark_id' => 6, 'text' => $text]);
        $response->assertStatus(401);

//        Pokus o komentovanie bez uvedenia zalozky
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer' . $token,
        ])->json('POST', '/api/comments', ['text' => $text]);
        $response->assertStatus(409);
    }

    public function testValidEditComment(){
        $text = $this->faker->text;
        $email = 'sally.smith@example.com';
        $password = 'Sally123!';
        $response = $this->json('POST', '/api/login', ['email' => $email, 'password' => $password]);
        $response->assertStatus(200);
        $token = $response->json()['data']['token'];

//        Uprava komentaru
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer' . $token,
        ])->json('PATCH', '/api/comments/1', ['text' => $text]);
        $response->assertStatus(200);
    }

    public function testInvalidEditComment(){
        $text = $this->faker->text;
        $email = 'sally.smith@example.com';
        $password = 'Sally123!';
        $response = $this->json('POST', '/api/login', ['email' => $email, 'password' => $password]);
        $response->assertStatus(200);
        $token = $response->json()['data']['token'];

//        Pokus o upravu komentaru, ktory vytvoril iny pouzivatel
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer' . $token,
        ])->json('PATCH', '/api/comments/3', ['text' => $text]);
        $response->assertStatus(401);

//        Pokus o upravu komentaru bez zadania textu
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer' . $token,
        ])->json('PATCH', '/api/comments/1', ['text' => '']);
        $response->assertStatus(409);
    }

    public function testValidDeleteComment(){
        $text = $this->faker->text;
        $email = 'sally.smith@example.com';
        $password = 'Sally123!';
        $response = $this->json('POST', '/api/login', ['email' => $email, 'password' => $password]);
        $response->assertStatus(200);
        $token = $response->json()['data']['token'];

//        Komentovanie vlastnej zalozky
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer' . $token,
        ])->json('POST', '/api/comments', ['bookmark_id' => 1, 'text' => $text]);
        $response->assertStatus(200);
        $comment = Comment::where('text', $text)->first();

//        Zmazanie komentaru pod vlastnou zalozkou
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer' . $token,
        ])->json('DELETE', '/api/comments/'.$comment->id);
        $response->assertStatus(200);

//        Komentovanie cudzej, verejnej zalozky
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer' . $token,
        ])->json('POST', '/api/comments', ['bookmark_id' => 5, 'text' => $text]);
        $response->assertStatus(200);
        $comment = Comment::where('text', $text)->first();

//        Zmazanie komentaru pod cudzou, verejnou zalozkou
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer' . $token,
        ])->json('DELETE', '/api/comments/'.$comment->id);
        $response->assertStatus(200);
    }

    public function testInvalidDeleteComment(){
        $email = 'sally.smith@example.com';
        $password = 'Sally123!';
        $response = $this->json('POST', '/api/login', ['email' => $email, 'password' => $password]);
        $response->assertStatus(200);
        $token = $response->json()['data']['token'];

//        Pokus o zmazanie neexistujuceho komentaru
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer' . $token,
        ])->json('DELETE', '/api/comments/666');
        $response->assertStatus(409);

//        Pokus o zmazanie komentara, ktory vytvoril iny clovek pod svojou zalozkou
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer' . $token,
        ])->json('DELETE', '/api/comments/3');
        $response->assertStatus(401);
    }
}

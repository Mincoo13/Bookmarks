<?php

namespace Tests\Feature;

use App\Category;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CategoryTest extends TestCase
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

    public function testNoToken()
    {
//        Pouzivatel sa prihlasi, vygeneruje sa token, avsak neposle sa dalej, preto sa pri zobrazeni profilu vrati chybova hlaska 401.
        $response = $this->json('POST', '/api/categories', ['name' => 'food']);
        $response->assertStatus(401);
        $response = $this->json('PATCH', '/api/categories/2', ['name' => 'food']);
        $response->assertStatus(401);
    }

    public function testValidCreateCategory(){
//        Vytvorenie kategorie
        $name = $this->faker->word." ".$this->faker->word;

        $email = 'sally.smith@example.com';
        $password = 'Sally123!';
        $response = $this->json('POST', '/api/login', ['email' => $email, 'password' => $password]);
        $response->assertStatus(200);
        $token = $response->json()['data']['token'];
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer' . $token,
        ])->json('POST', '/api/categories', ['name' => $name]);
        $response->assertStatus(200);

    }

    public function testInvalidCreateCategory(){
//        Pokus o upravu kategorie na nazov, pod ktorym uz ina kategoria existuje.
        $email = 'sally.smith@example.com';
        $password = 'Sally123!';
        $response = $this->json('POST', '/api/login', ['email' => $email, 'password' => $password]);
        $response->assertStatus(200);
        $token = $response->json()['data']['token'];
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer' . $token,
        ])->json('POST', '/api/categories', ['name' => 'Škola']);
        $response->assertStatus(409);
    }

    public function testValidEditCategory(){
//        Zmena nazvu kategorie
        $name = "animal planet";
        $email = 'sally.smith@example.com';
        $password = 'Sally123!';
        $response = $this->json('POST', '/api/login', ['email' => $email, 'password' => $password]);
        $response->assertStatus(200);
        $token = $response->json()['data']['token'];
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer' . $token,
        ])->json('PATCH', '/api/categories/2', ['name' => $name]);
        $response->assertStatus(200);

//        Spatna zmena na povodny nazov.
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer' . $token,
        ])->json('PATCH', '/api/categories/2', ['name' => "animals"]);
        $response->assertStatus(200);
    }

    public function testInvalidEditCategory(){
        $name = "Vývoj Webu";
        $email = 'sally.smith@example.com';
        $password = 'Sally123!';
        $response = $this->json('POST', '/api/login', ['email' => $email, 'password' => $password]);
        $response->assertStatus(200);
        $token = $response->json()['data']['token'];
//        Pokus o zmenu na nazov, pod ktorym uz existuje kategoria.
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer' . $token,
        ])->json('PATCH', '/api/categories/2', ['name' => $name]);
        $response->assertStatus(409);

//        Pokus o zmenu kategorie, ktora nepatri danemu pouzivatelovi.
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer' . $token,
        ])->json('PATCH', '/api/categories/4', ['name' => $name]);
        $response->assertStatus(401);

//        Pokus o zmenu neexistujucej kategorie.
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer' . $token,
        ])->json('PATCH', '/api/categories/9999', ['name' => $name]);
        $response->assertStatus(404);
    }

    public function testValidDeleteCategory(){
        $email = 'sally.smith@example.com';
        $password = 'Sally123!';
        $name = 'test';
        $response = $this->json('POST', '/api/login', ['email' => $email, 'password' => $password]);
        $response->assertStatus(200);
        $token = $response->json()['data']['token'];

//        Vytvorenie novej kategorie
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer' . $token,
        ])->json('POST', '/api/categories', ['name' => $name]);
        $response->assertStatus(200);
        $id = Category::where('name', $name)->first()->id;

//        Zmazanie danej kategorie
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer' . $token,
        ])->json('DELETE', '/api/categories/'.$id);
        $response->assertStatus(200);
    }

    public function testInvalidDeleteCategory(){
        $email = 'sally.smith@example.com';
        $password = 'Sally123!';
        $response = $this->json('POST', '/api/login', ['email' => $email, 'password' => $password]);
        $response->assertStatus(200);
        $token = $response->json()['data']['token'];

//        Pokus o zmazanie kategorie, ktora patri inemu pouzivatelovi
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer' . $token,
        ])->json('DELETE', '/api/categories/4');
        $response->assertStatus(401);

//        Pokus o zmazanie neexistujucej kategorie
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer' . $token,
        ])->json('DELETE', '/api/categories/999');
        $response->assertStatus(409);

//        Pokus o zmazanie kategorie, ktorej su priradene zalozky
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer' . $token,
        ])->json('DELETE', '/api/categories/2');
        $response->assertStatus(409);
    }

    public function testValidShowAllCategories(){
//        Zobrazenie vsetkych kategorii
        $email = 'sally.smith@example.com';
        $password = 'Sally123!';
        $response = $this->json('POST', '/api/login', ['email' => $email, 'password' => $password]);
        $response->assertStatus(200);
        $token = $response->json()['data']['token'];
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer' . $token,
        ])->json('GET', '/api/categories');
        $response->assertStatus(200);
        $response->assertJson([['id' => 1],['id' => 2],['id' => 3]]);
    }

    public function testValidShowCategory(){
        $email = 'sally.smith@example.com';
        $password = 'Sally123!';
        $response = $this->json('POST', '/api/login', ['email' => $email, 'password' => $password]);
        $response->assertStatus(200);
        $token = $response->json()['data']['token'];
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer' . $token,
        ])->json('GET', '/api/categories/1');
        $response->assertStatus(200);
    }
}

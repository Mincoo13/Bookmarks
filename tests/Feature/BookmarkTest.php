<?php

namespace Tests\Feature;

use App\Bookmark;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BookmarkTest extends TestCase
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
//        Pouzivatel sa prihlasi, vygeneruje sa token, avsak neposle sa dalej, preto sa pri zobrazeni profilu vrati chybova hlaska 401.
        $response = $this->json('POST', '/api/login', ['email' => 'sally.smith@example.com', 'password' => 'Sally123!']);
        $response->assertStatus(200);
        $response = $this->json('POST', 'api/bookmarks', ['name' => 'Facebook', 'url' => 'Facebook.com', 'description' => 'social network', 'category_id' => 1, 'isVisible' => 1]);
        $response->assertStatus(401);
        $response = $this->json('PATCH', 'api/bookmarks/1', ['name' => 'Facebook', 'url' => 'Facebook.com', 'description' => 'social network', 'category_id' => 1, 'isVisible' => 1]);
        $response->assertStatus(401);
        $response = $this->json('PATCH', '/api/bookmarks/1/mark-read');
        $response->assertStatus(401);
        $response = $this->json('POST', '/api/search-bookmarks',['text' => '.com', 'global' => 0, 'read'=> 1]);
        $response->assertStatus(401);
        $response = $this->json('DELETE', '/api/bookmarks/1');
        $response->assertStatus(401);

    }

    public function testValidCreateBookmark(){
//        Uspesne vytvorenie zalozky
        $name = $this->faker->word." ".$this->faker->word;
        $url = $this->faker->url;
        $description = $this->faker->text;
        $email = 'sally.smith@example.com';
        $password = 'Sally123!';
        $response = $this->json('POST', '/api/login', ['email' => $email, 'password' => $password]);
        $response->assertStatus(200);
        $token = $response->json()['data']['token'];
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer' . $token,
        ])->json('POST', '/api/bookmarks', ['name' => $name, 'url' => $url, 'description' => $description, 'category_id' => 1, 'isVisible' => 1]);
        $response->assertStatus(200);

        $name = $this->faker->word." ".$this->faker->word;
        $url = $this->faker->url;
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer' . $token,
        ])->json('POST', '/api/bookmarks', ['name' => $name, 'url' => $url]);
        $response->assertStatus(200);
    }

    public function testInvalidCreateBookmark(){
        $email = 'sally.smith@example.com';
        $password = 'Sally123!';
        $name = 'Materna';
        $response = $this->json('POST', '/api/login', ['email' => $email, 'password' => $password]);
        $response->assertStatus(200);

//        Pokus o vytvorenie zalozky s existujucim menom
        $token = $response->json()['data']['token'];
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer' . $token,
        ])->json('POST', '/api/bookmarks', ['name' => $name, 'url' => 'maternauni.slack.com', 'description' => 'description', 'category_id' => 1]);
        $response->assertStatus(409);

//        Pokus o vytvorenie zalozky s kategoriou, ktoru vytvoril iny pouzivatel
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer' . $token,
        ])->json('POST', '/api/bookmarks', ['name' => 'Test1', 'url' => 'maternauni.slack.com', 'description' => 'description', 'category_id' => 4]);
        $response->assertStatus(401);

//        Pokus o vytvorenie zalozky bez zadania nazvu
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer' . $token,
        ])->json('POST', '/api/bookmarks', ['url' => 'maternauni.slack.com', 'description' => 'description', 'category_id' => 1]);
        $response->assertStatus(409);

//        Pokus o vytvorenie zalozky bez uvedenia url
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer' . $token,
        ])->json('POST', '/api/bookmarks', ['name' => 'Test2', 'description' => 'description', 'category_id' => 1]);
        $response->assertStatus(409);
    }

    public function testValidEditBookmark(){
//        Zmena zalozky
        $name = $this->faker->word." ".$this->faker->word;
        $url = $this->faker->url;
        $description = $this->faker->text;
        $email = 'sally.smith@example.com';
        $password = 'Sally123!';
        $response = $this->json('POST', '/api/login', ['email' => $email, 'password' => $password]);
        $response->assertStatus(200);
        $token = $response->json()['data']['token'];
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer' . $token,
        ])->json('PATCH', '/api/bookmarks/1', ['name' => $name, 'url' => $url, 'description' => $description, 'category_id' => 1]);
        $response->assertStatus(200);

//        Zmena zalozky na povodny nazov a url
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer' . $token,
        ])->json('PATCH', '/api/bookmarks/1', ['name' => 'Materna', 'url' => 'maternauni.slack.com', 'description' => $description, 'category_id' => 1]);
        $response->assertStatus(200);
    }

    public function testInvalidEditBookmark(){
        $description = $this->faker->text;
        $email = 'sally.smith@example.com';
        $password = 'Sally123!';
        $response = $this->json('POST', '/api/login', ['email' => $email, 'password' => $password]);
        $response->assertStatus(200);
        $token = $response->json()['data']['token'];
//        Pokus o zmenu zalozky bez zadania nazvu
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer' . $token,
        ])->json('PATCH', '/api/bookmarks/1', ['url' => 'test.com', 'description' => $description, 'category_id' => 1]);
        $response->assertStatus(409);

//        Pokus o zmenu zalozky bez zadania url
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer' . $token,
        ])->json('PATCH', '/api/bookmarks/1', ['name' => 'test2', 'description' => $description, 'category_id' => 1]);
        $response->assertStatus(409);

//        Pokus o zmenu zalozky, ktora nepatri danemu pouzivatelovi
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer' . $token,
        ])->json('PATCH', '/api/bookmarks/5', ['url' => 'test.com', 'description' => $description, 'category_id' => 1]);
        $response->assertStatus(401);

//        Pokus o zmenu nazvu zalozky na nazov, pod ktorym uz ina zalozka existuje
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer' . $token,
        ])->json('PATCH', '/api/bookmarks/1', ['name' => 'Facebook', 'url' => 'test.com', 'description' => $description, 'category_id' => 1]);
        $response->assertStatus(409);

//        Pokus o zmenu zalozky s kategoriou, ktoru nevytvoril dany pouzivatel
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer' . $token,
        ])->json('PATCH', '/api/bookmarks/1', ['name' => 'test123', 'url' => 'test.com', 'description' => $description, 'category_id' => 4]);
        $response->assertStatus(401);
    }

    public function testValidMarkReadFlag(){
        $email = 'sally.smith@example.com';
        $password = 'Sally123!';
        $response = $this->json('POST', '/api/login', ['email' => $email, 'password' => $password]);
        $response->assertStatus(200);
        $token = $response->json()['data']['token'];
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer' . $token,
        ])->json('PATCH', '/api/bookmarks/1/mark-read');
        $response->assertStatus(200);

        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer' . $token,
        ])->json('PATCH', '/api/bookmarks/1/mark-read');
        $response->assertStatus(200);
    }

    public function testInvalidMarkReadFlag(){
        $email = 'sally.smith@example.com';
        $password = 'Sally123!';
        $response = $this->json('POST', '/api/login', ['email' => $email, 'password' => $password]);
        $response->assertStatus(200);
        $token = $response->json()['data']['token'];

//        Pokus o oznacenie cudzej zalozky
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer' . $token,
        ])->json('PATCH', '/api/bookmarks/5/mark-read');
        $response->assertStatus(401);

//        Pokus o oznacenie neexistujucej zalozky
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer' . $token,
        ])->json('PATCH', '/api/bookmarks/666/mark-read');
        $response->assertStatus(409);
    }

    public function testValidDeleteBookmark(){
        $email = 'sally.smith@example.com';
        $password = 'Sally123!';
        $response = $this->json('POST', '/api/login', ['email' => $email, 'password' => $password]);
        $response->assertStatus(200);
        $token = $response->json()['data']['token'];

//        Najskor si vytvorim bookmark
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer' . $token,
        ])->json('POST', '/api/bookmarks', ['name' => 'test_zmazanie', 'url' => 'zmazanie.com', 'description' => 'zmazanie', 'category_id' => 1, 'isVisible' => 1]);
        $id = Bookmark::where('name','test_zmazanie')->first()->id;
        $response->assertStatus(200);


//        Zmazanie bookmarku
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer' . $token,
        ])->json('DELETE', '/api/bookmarks/'.$id);
        $response->assertStatus(200);
    }

    public function testInvalidDeleteBookmark(){
        $email = 'sally.smith@example.com';
        $password = 'Sally123!';
        $response = $this->json('POST', '/api/login', ['email' => $email, 'password' => $password]);
        $response->assertStatus(200);
        $token = $response->json()['data']['token'];

        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer' . $token,
        ])->json('DELETE', '/api/bookmarks/666');
        $response->assertStatus(409);

        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer' . $token,
        ])->json('DELETE', '/api/bookmarks/5');
        $response->assertStatus(401);
    }

    public function testValidSearchBookmakrs(){
        $email = 'sally.smith@example.com';
        $password = 'Sally123!';
        $response = $this->json('POST', '/api/login', ['email' => $email, 'password' => $password]);
        $response->assertStatus(200);
        $token = $response->json()['data']['token'];

        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer' . $token,
        ])->json('POST', '/api/search-bookmarks',['text' => 'Materna', 'global' => 1, 'read'=> 1, 'category' => 'food']);
        $response->assertStatus(200);

        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer' . $token,
        ])->json('POST', '/api/search-bookmarks',['text' => 'Facebook', 'global' => 0, 'read'=> 1, 'category' => 'animals']);
        $response->assertStatus(200);

        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer' . $token,
        ])->json('POST', '/api/search-bookmarks',['text' => 'dolor', 'global' => 1, 'read'=> 0]);
        $response->assertStatus(200);

        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer' . $token,
        ])->json('POST', '/api/search-bookmarks',['text' => '.com', 'global' => 0, 'read'=> 1]);
        $response->assertStatus(200);
    }

    public function testInvalidSearchBookmarks(){
        $email = 'sally.smith@example.com';
        $password = 'Sally123!';
        $response = $this->json('POST', '/api/login', ['email' => $email, 'password' => $password]);
        $response->assertStatus(200);
        $token = $response->json()['data']['token'];

        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer' . $token,
        ])->json('POST', '/api/search-bookmarks',['text' => 'nahodny retazec znakov', 'global' => 1, 'read'=> 1]);
        $response->assertStatus(409);

        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer' . $token,
        ])->json('POST', '/api/search-bookmarks',['text' => 'nahodny retazec znakov', 'global' => 1, 'read'=> 0]);
        $response->assertStatus(409);

        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer' . $token,
        ])->json('POST', '/api/search-bookmarks',['text' => 'Materna', 'global' => 1, 'read'=> 0, 'category' => 'nature']);
        $response->assertStatus(409);

        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer' . $token,
        ])->json('POST', '/api/search-bookmarks',['text' => 'Google', 'global' => 0, 'read'=> 1, 'category' => 'animals']);
        $response->assertStatus(409);
    }
}

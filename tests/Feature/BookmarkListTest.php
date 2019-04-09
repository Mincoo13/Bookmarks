<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BookmarkListTest extends TestCase
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
        $response = $this->json('POST', '/api/bookmark-lists', ['name' => 'text']);
        $response->assertStatus(401);
        $response = $this->json('POST', '/api/bookmark-lists/1', ['name' => 'text']);
        $response->assertStatus(401);
        $response = $this->json('PATCH', '/api/bookmark-lists/1/order', ['bookmark_id' => 1, 'order' => 4]);
        $response->assertStatus(401);
        $response = $this->json('PATCH', '/api/bookmark-lists/1', ['bookmark_id' => 3]);
        $response->assertStatus(401);
    }

    public function testValidCreateBookmarkList(){
        $name = $this->faker->word." ".$this->faker->word;
        $email = 'sally.smith@example.com';
        $password = 'Sally123!';
        $response = $this->json('POST', '/api/login', ['email' => $email, 'password' => $password]);
        $response->assertStatus(200);
        $token = $response->json()['data']['token'];

//        Vytvorenie zoznamu
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer' . $token,
        ])->json('POST', '/api/bookmark-lists', ['name' => $name, 'isVisible' => 1]);
        $response->assertStatus(200);

//        Vytvorenie zoznamu bez urcenia viditelnosti
        $name = $this->faker->word." ".$this->faker->word;
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer' . $token,
        ])->json('POST', '/api/bookmark-lists', ['name' => $name]);
        $response->assertStatus(200);
    }

   public function testInvalidCreateBookmarkList(){
       $email = 'sally.smith@example.com';
       $password = 'Sally123!';
       $response = $this->json('POST', '/api/login', ['email' => $email, 'password' => $password]);
       $response->assertStatus(200);
       $token = $response->json()['data']['token'];

//       Pokus o pridanie zoznamu s nazvom, pod ktorym existuje u uzivatela uz iny zoznam
       $response = $this->withHeaders([
           'Accept' => 'application/json',
           'Content-Type' => 'application/json',
           'Authorization' => 'Bearer' . $token,
       ])->json('POST', '/api/bookmark-lists', ['name' => 'School', 'isVisible' => 1]);
       $response->assertStatus(409);

//       Pokus o pridanie zoznamu bez nazvu
       $response = $this->withHeaders([
           'Accept' => 'application/json',
           'Content-Type' => 'application/json',
           'Authorization' => 'Bearer' . $token,
       ])->json('POST', '/api/bookmark-lists', ['isVisible' => 1]);
       $response->assertStatus(409);
   }

   public function testValidAddBookmarkToList(){
       $email = 'sally.smith@example.com';
       $password = 'Sally123!';
       $response = $this->json('POST', '/api/login', ['email' => $email, 'password' => $password]);
       $response->assertStatus(200);
       $token = $response->json()['data']['token'];

//       Pridanie zalozky
       $response = $this->withHeaders([
           'Accept' => 'application/json',
           'Content-Type' => 'application/json',
           'Authorization' => 'Bearer' . $token,
       ])->json('POST', '/api/bookmark-lists/1', ['bookmark_id' => 7]);
       $response->assertStatus(200);
//       Zmazanie pridanej zalozky kvoli buducim testom
       DB::table('bookmarklists_bookmarks')->where([
           ['bookmark_id', '=', 7],
           ['bookmarklist_id', '=', 1],
       ])->delete();

//       Pridanie zalozky
       $response = $this->withHeaders([
           'Accept' => 'application/json',
           'Content-Type' => 'application/json',
           'Authorization' => 'Bearer' . $token,
       ])->json('POST', '/api/bookmark-lists/2', ['bookmark_id' => 1]);
       $response->assertStatus(200);
//       Zmazanie zalozky kvoli buducim testom
       DB::table('bookmarklists_bookmarks')->where([
           ['bookmark_id', '=', 1],
           ['bookmarklist_id', '=', 2],
       ])->delete();
   }

   public function testInvalidAddBookmarkToList(){
       $email = 'sally.smith@example.com';
       $password = 'Sally123!';
       $response = $this->json('POST', '/api/login', ['email' => $email, 'password' => $password]);
       $response->assertStatus(200);
       $token = $response->json()['data']['token'];

//       Pokus o pridanie nicoho do zoznamu
       $response = $this->withHeaders([
           'Accept' => 'application/json',
           'Content-Type' => 'application/json',
           'Authorization' => 'Bearer' . $token,
       ])->json('POST', '/api/bookmark-lists/2');
       $response->assertStatus(409);

//       Pokus o pridanie do cudzieho zoznamu
       $response = $this->withHeaders([
           'Accept' => 'application/json',
           'Content-Type' => 'application/json',
           'Authorization' => 'Bearer' . $token,
       ])->json('POST', '/api/bookmark-lists/4', ['bookmark_id' => 1]);
       $response->assertStatus(401);

//       Pokus o pridanie cudzej zalozky do zoznamu
       $response = $this->withHeaders([
           'Accept' => 'application/json',
           'Content-Type' => 'application/json',
           'Authorization' => 'Bearer' . $token,
       ])->json('POST', '/api/bookmark-lists/2', ['bookmark_id' => 5]);
       $response->assertStatus(401);

//       Pokus o pridanie do neexistujuceho zoznamu
       $response = $this->withHeaders([
           'Accept' => 'application/json',
           'Content-Type' => 'application/json',
           'Authorization' => 'Bearer' . $token,
       ])->json('POST', '/api/bookmark-lists/666', ['bookmark_id' => 1]);
       $response->assertStatus(409);

//       Pokus o pridanie neexistujucej zalozky do zoznamu
       $response = $this->withHeaders([
           'Accept' => 'application/json',
           'Content-Type' => 'application/json',
           'Authorization' => 'Bearer' . $token,
       ])->json('POST', '/api/bookmark-lists/2', ['bookmark_id' => 666]);
       $response->assertStatus(409);

//       Pokus o pridanie duplikatu
       $response = $this->withHeaders([
           'Accept' => 'application/json',
           'Content-Type' => 'application/json',
           'Authorization' => 'Bearer' . $token,
       ])->json('POST', '/api/bookmark-lists/1', ['bookmark_id' => 1]);
       $response->assertStatus(409);
   }

   public function testValidSetBookmarkOrder(){
       $email = 'sally.smith@example.com';
       $password = 'Sally123!';
       $response = $this->json('POST', '/api/login', ['email' => $email, 'password' => $password]);
       $response->assertStatus(200);
       $token = $response->json()['data']['token'];

//       Zalozka s id sa presunie na 4. miesto
       $response = $this->withHeaders([
           'Accept' => 'application/json',
           'Content-Type' => 'application/json',
           'Authorization' => 'Bearer' . $token,
       ])->json('PATCH', '/api/bookmark-lists/1/order', ['bookmark_id' => 1, 'order' => 4]);
       $response->assertStatus(200);

//      Spatna zmena poradia
       $response = $this->withHeaders([
           'Accept' => 'application/json',
           'Content-Type' => 'application/json',
           'Authorization' => 'Bearer' . $token,
       ])->json('PATCH', '/api/bookmark-lists/1/order', ['bookmark_id' => 1, 'order' => 1]);
       $response->assertStatus(200);
   }

   public function testInvalidSetBookmarkOrder(){
       $email = 'sally.smith@example.com';
       $password = 'Sally123!';
       $response = $this->json('POST', '/api/login', ['email' => $email, 'password' => $password]);
       $response->assertStatus(200);
       $token = $response->json()['data']['token'];

//      Neexistujuci zoznam
       $response = $this->withHeaders([
           'Accept' => 'application/json',
           'Content-Type' => 'application/json',
           'Authorization' => 'Bearer' . $token,
       ])->json('PATCH', '/api/bookmark-lists/666/order', ['bookmark_id' => 1, 'order' => 4]);
       $response->assertStatus(409);

//       Zalozka sa v zozname nenachadza
       $response = $this->withHeaders([
           'Accept' => 'application/json',
           'Content-Type' => 'application/json',
           'Authorization' => 'Bearer' . $token,
       ])->json('PATCH', '/api/bookmark-lists/1/order', ['bookmark_id' => 6, 'order' => 4]);
       $response->assertStatus(409);

//      Zoznam, ktory nepatri danemu pouzivatelovi
       $response = $this->withHeaders([
           'Accept' => 'application/json',
           'Content-Type' => 'application/json',
           'Authorization' => 'Bearer' . $token,
       ])->json('PATCH', '/api/bookmark-lists/4/order', ['bookmark_id' => 5, 'order' => 1]);
       $response->assertStatus(401);

//       Neexistujuce poradie
       $response = $this->withHeaders([
           'Accept' => 'application/json',
           'Content-Type' => 'application/json',
           'Authorization' => 'Bearer' . $token,
       ])->json('PATCH', '/api/bookmark-lists/4/order', ['bookmark_id' => 1, 'order' => 666]);
       $response->assertStatus(409);
   }

   public function testValidDeleteBookmark(){
       $email = 'sally.smith@example.com';
       $password = 'Sally123!';
       $response = $this->json('POST', '/api/login', ['email' => $email, 'password' => $password]);
       $response->assertStatus(200);
       $token = $response->json()['data']['token'];

//       Zmazanie bookmarku
       $response = $this->withHeaders([
           'Accept' => 'application/json',
           'Content-Type' => 'application/json',
           'Authorization' => 'Bearer' . $token,
       ])->json('PATCH', '/api/bookmark-lists/1', ['bookmark_id' => 1]);
       $response->assertStatus(200);

//       Spatne pridanie bookmarku do zoznamu
       $response = $this->withHeaders([
           'Accept' => 'application/json',
           'Content-Type' => 'application/json',
           'Authorization' => 'Bearer' . $token,
       ])->json('POST', '/api/bookmark-lists/1', ['bookmark_id' => 1]);
       $response->assertStatus(200);
   }

   public function testInvalidDeleteBookmark(){
       $email = 'sally.smith@example.com';
       $password = 'Sally123!';
       $response = $this->json('POST', '/api/login', ['email' => $email, 'password' => $password]);
       $response->assertStatus(200);
       $token = $response->json()['data']['token'];

//       Zmazanie v zozname, ktory patri inemu pouzivatelovi
       $response = $this->withHeaders([
           'Accept' => 'application/json',
           'Content-Type' => 'application/json',
           'Authorization' => 'Bearer' . $token,
       ])->json('PATCH', '/api/bookmark-lists/4', ['bookmark_id' => 6]);
       $response->assertStatus(401);

//       Zmazanie zalozky, ktora sa nenachadza v zozname
       $response = $this->withHeaders([
           'Accept' => 'application/json',
           'Content-Type' => 'application/json',
           'Authorization' => 'Bearer' . $token,
       ])->json('PATCH', '/api/bookmark-lists/1', ['bookmark_id' => 6]);
       $response->assertStatus(409);
   }
}

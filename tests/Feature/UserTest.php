<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExample()
    {
        $this->assertTrue(true);
    }

    public function testValidProfile()
    {
//        Uzivatel sa prihlasi, vygeneruje token, ktory potom posle na GET profile, ktory vrati JSON profilu pouzivatela.
        $email = 'bla@bla.com';
        $password = 'Ahoj123!';
        $id = '51';
        $response = $this->json('POST', '/api/login', ['email' => $email, 'password' => $password]);
        $response->assertStatus(200);
        $token = $response->json()['data']['token'];
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer' . $token,
        ])->json('GET', '/api/profile');
        $response->assertStatus(200);
//        Overenie, ci udaje, ktore vratil GET profile sedia s prihlasenym pouzivatelom.
        $response->assertJson(['id' => $id, 'email' => $email]);
    }

    public function testNoTokenProfile()
    {
//        Pouzivatel sa prihlasi, vygeneruje sa token, avsak neposle sa dalej, preto sa pri zobrazeni profilu vrati chybova hlaska 401.
        $response = $this->json('POST', '/api/login', ['email' => 'bla@bla.com', 'password' => 'Ahoj123!']);
        $response->assertStatus(200);
        $response = $this->json('GET', '/api/profile');
        $response->assertStatus(401);
    }

    public function testValidEditProfile()
    {
//        Pouzivatel sa prihlasi, vygeneruje sa token, ktory sa posle na PATCH profile, ktoremu sa poslu udaje na zmenu mena a priezviska
//        Ak je vsetko spravne, vrati sa 200.
        $email = 'bla@bla.com';
        $password = 'Ahoj123!';
        $id = '51';
        $response = $this->json('POST', '/api/login', ['email' => $email, 'password' => $password]);
        $response->assertStatus(200);
        $token = $response->json()['data']['token'];
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer' . $token,
        ])->json('PATCH', '/api/profile', ['name' => 'change', 'surname' => 'changed']);
        $response->assertStatus(200);
        $response->assertJson(['id' => $id, 'email' => $email, 'name' => 'change', 'surname' => 'changed']);

//        Opatovna zmena udajov na povodne hodnoty
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer' . $token,
        ])->json('PATCH', '/api/profile', ['name' => 'mino', 'surname' => 'mino']);
        $response->assertStatus(200);
        $response->assertJson(['id' => $id, 'email' => $email, 'name' => 'mino', 'surname' => 'mino']);
    }

    public function testInvalidEditProfile()
    {
//        Pouzivatel sa prihlasi, vygeneruje sa token, ktory sa dalej posle na PATCH profile, avsak hodnoty mena a priezviska su NULL, co
//        nesmie byt validne.
        $email = 'bla@bla.com';
        $password = 'Ahoj123!';
        $id = '51';
        $response = $this->json('POST', '/api/login', ['email' => $email, 'password' => $password]);
        $response->assertStatus(200);
        $token = $response->json()['data']['token'];
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer' . $token,
        ])->json('PATCH', '/api/profile', ['name' => NULL, 'surname' => NULL]);
        $response->assertStatus(500);

//        Odhlasenie pouzivatela
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer' . $token,
        ])->json('GET', '/api/logout');
        $response->assertStatus(200);
//        Pokus o PATCH profile bez zaslania tokenu.

        $response = $this->json('POST', '/api/login', ['email' => $email, 'password' => $password]);
        $response->assertStatus(200);
        $response = $this->json('PATCH', '/api/profile', ['name' => 'something', 'surname' => 'something']);
        $response->assertStatus(401);

    }

    public function testValidAdminEditProfile()
    {
//        Prihlasenie admina a zmena udajov u pouzivatela pod ID 1.
//        isAdmin = 1
        $email = 'bla@bla.com';
        $password = 'Ahoj123!';
        $response = $this->json('POST', '/api/login', ['email' => $email, 'password' => $password]);
        $response->assertStatus(200);
        $token = $response->json()['data']['token'];
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer' . $token,
        ])->json('PATCH', '/api/profile/1', ['name' => '1changed', 'surname' => '1changed']);
        $response->assertStatus(200);
        $response->assertJson(['id' => '1', 'name' => '1changed', 'surname' => '1changed']);
    }

    public function testInvalidAdminEditProfile()
    {
//        prihlasenie bezneho pouzivatela, ktory nema privilegia na zmenu udajov u ostatnych pouzivatelov.
//        isAdmin = 0
        $email = 'sally.smith@gmail.com';
        $password = 'Sally123!';
        $response = $this->json('POST', '/api/login', ['email' => $email, 'password' => $password]);
        $response->assertStatus(200);
        $token = $response->json()['data']['token'];
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer' . $token,
        ])->json('PATCH', '/api/profile/1', ['name' => '1changed', 'surname' => '1changed']);
        $response->assertStatus(401);
    }
}

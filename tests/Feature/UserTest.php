<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use App\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
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

    public function testValidProfile()
    {
//        Uzivatel sa prihlasi, vygeneruje token, ktory potom posle na GET profile, ktory vrati JSON profilu pouzivatela.
        $email = 'admin@admin.com';
        $password = 'Ahoj123!';
        $id = '1';
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

    public function testNoToken()
    {
//        Pouzivatel sa prihlasi, vygeneruje sa token, avsak neposle sa dalej, preto sa pri zobrazeni profilu vrati chybova hlaska 401.
        $response = $this->json('POST', '/api/login', ['email' => 'sally.smith@example.com', 'password' => 'Sally123!']);
        $response->assertStatus(200);
        $response = $this->json('GET', '/api/profile');
        $response->assertStatus(401);
        $response = $this->json('PATCH', '/api/profile',['name' => 'change', 'surname' => 'changed']);
        $response->assertStatus(401);
        $response = $this->json('PATCH', '/api/profile/1', ['name' => '1changed', 'surname' => '1changed']);
        $response->assertStatus(401);
        $response = $this->json('DELETE', '/api/users/3');
        $response->assertStatus(401);
        $response = $this->json('PATCH', '/api/users/changepassword', ['oldPassword' => 'Sally123!', 'newPassword' => 'Newpass123!', 'confirm' => 'Newpass123!']);
        $response->assertStatus(401);
        $response = $this->json('PATCH', '/api/users/4/deactivate');
        $response->assertStatus(401);
        $response = $this->json('PATCH', '/api/users/4/activate');
        $response->assertStatus(401);
    }

    public function testValidEditProfile()
    {
//        Pouzivatel sa prihlasi, vygeneruje sa token, ktory sa posle na PATCH profile, ktoremu sa poslu udaje na zmenu mena a priezviska
//        Ak je vsetko spravne, vrati sa 200.
        $email = 'admin@admin.com';
        $password = 'Ahoj123!';
        $id = '1';
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
        ])->json('PATCH', '/api/profile', ['name' => 'Admin', 'surname' => 'Admin']);
        $response->assertStatus(200);
        $response->assertJson(['id' => $id, 'email' => $email, 'name' => 'Admin', 'surname' => 'Admin']);
    }

    public function testInvalidEditProfile()
    {
//        Pouzivatel sa prihlasi, vygeneruje sa token, ktory sa dalej posle na PATCH profile, avsak hodnoty mena a priezviska su NULL, co
//        nesmie byt validne.
        $email = 'admin@admin.com';
        $password = 'Ahoj123!';
        $id = '1';
        $response = $this->json('POST', '/api/login', ['email' => $email, 'password' => $password]);
        $response->assertStatus(200);
        $token = $response->json()['data']['token'];
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer' . $token,
        ])->json('PATCH', '/api/profile', ['name' => NULL, 'surname' => NULL]);
        $response->assertStatus(409);

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
        $email = 'admin@admin.com';
        $password = 'Ahoj123!';
        $response = $this->json('POST', '/api/login', ['email' => $email, 'password' => $password]);
        $response->assertStatus(200);
        $token = $response->json()['data']['token'];
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer' . $token,
        ])->json('PATCH', '/api/profile/3', ['name' => '3changed', 'surname' => '3changed']);
        $response->assertStatus(200);
        $response->assertJson(['id' => '3', 'name' => '3changed', 'surname' => '3changed']);
    }

    public function testInvalidAdminEditProfile()
    {
//        prihlasenie bezneho pouzivatela, ktory nema privilegia na zmenu udajov u ostatnych pouzivatelov.
//        isAdmin = 0
        $email = 'sally.smith@example.com';
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

    public function testValidUserDelete()
    {
//        Najskor zaregistruje noveho pouzivatela.
        $name = $this->faker->firstName();
        $surname = $this->faker->lastName();
        $email = $this->faker->unique()->safeEmail();
        $password='$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm';
        $response = $this->json('POST', 'api/register-user', ['name' => $name,'surname' => $surname,'email' => $email, 'password' => $password, 'password_confirmation' => $password]);
        $response
            ->assertStatus(201);
        $id = User::where('email',$email)->first()->id;

//        Prihlasenie admina a zmazanie pouzivatela, ktory bol novo vytvoreny pri registracii.
        $email = 'admin@admin.com';
        $password = 'Ahoj123!';
        $response = $this->json('POST', '/api/login', ['email' => $email, 'password' => $password]);
        $response->assertStatus(200);
        $token = $response->json()['data']['token'];
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer' . $token,
        ])->json('DELETE', '/api/users/'.$id);
        $response->assertStatus(200);
    }

    public function testInvalidUserDelete()
    {
//        Prihlasenie bezneho pouzivatela a pokus o zmazanie pouzivatela s id 3.
        $email = 'sally.smith@example.com';
        $password = 'Sally123!';
        $response = $this->json('POST', '/api/login', ['email' => $email, 'password' => $password]);
        $response->assertStatus(200);
        $token = $response->json()['data']['token'];
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer' . $token,
        ])->json('DELETE', '/api/users/3');
        $response->assertStatus(401);
    }

    public function testValidPasswordChange(){
//        Prihlasenie pouzivatela
        $email = 'sally.smith@example.com';
        $password = 'Sally123!';
        $response = $this->json('POST', '/api/login', ['email' => $email, 'password' => $password]);
        $response->assertStatus(200);
        $token = $response->json()['data']['token'];
//        Zmena hesla zo Sally123! na Newpass123!
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer' . $token,
        ])->json('PATCH', '/api/users/changepassword', ['oldPassword' => $password, 'newPassword' => 'Newpass123!', 'confirmPassword' => 'Newpass123!']);
        $response->assertStatus(200);
        $response->assertSee('Heslo bolo zmenene.');
//        Spatna zmena na povodne heslo
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer' . $token,
        ])->json('PATCH', '/api/users/changepassword', ['oldPassword' => 'Newpass123!', 'newPassword' => $password, 'confirmPassword' => $password]);
        $response->assertStatus(200);
        $response->assertSee('Heslo bolo zmenene.');
    }

    public function testInvalidPasswordChange(){
        $email = 'sally.smith@example.com';
        $password = 'Sally123!';
        $response = $this->json('POST', '/api/login', ['email' => $email, 'password' => $password]);
        $response->assertStatus(200);
        $token = $response->json()['data']['token'];
//        Zadane nespravne aktualne heslo
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer' . $token,
        ])->json('PATCH', '/api/users/changepassword', ['oldPassword' => 'Nespravneheslo', 'newPassword' => 'Newpass123!', 'confirmPassword' => 'Newpass123!']);
        $response->assertStatus(500);
        $response->assertSee('Aktualne heslo je nespravne.');

//        Nove heslo sa nezhoduje s potvrdzovacim heslom
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer' . $token,
        ])->json('PATCH', '/api/users/changepassword', ['oldPassword' => $password, 'newPassword' => 'Newpass1234!', 'confirmPassword' => 'Newpass123!']);
        $response->assertStatus(500);
        $response->assertSee('Nove hesla sa nezhoduju.');

//        Nove heslo nesplna poziadavky silneho hesla
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer' . $token,
        ])->json('PATCH', '/api/users/changepassword', ['oldPassword' => $password, 'newPassword' => 'Newpass', 'confirmPassword' => 'Newpass']);
        $response->assertStatus(500);
        $response->assertSee('Nove heslo nesplna poziadavky dostatocne silneho hesla.');

//        Nove heslo je rovnake ako aktualne heslo
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer' . $token,
        ])->json('PATCH', '/api/users/changepassword', ['oldPassword' => $password, 'newPassword' => $password, 'confirmPassword' => $password]);
        $response->assertStatus(500);
        $response->assertSee('Nove heslo nemoze byt rovnake ako stare heslo.');
    }

    public function testValidActivation(){
//        Prihlasenie admina
        $email = 'admin@admin.com';
        $password = 'Ahoj123!';
        $id = 2;
        $response = $this->json('POST', '/api/login', ['email' => $email, 'password' => $password]);
        $response->assertStatus(200);
        $token = $response->json()['data']['token'];

//        Deaktivacia pouzivatela s id 2
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer' . $token,
        ])->json('PATCH', '/api/users/2/deactivate');
        $response->assertStatus(200);
        $response->assertJson(['id' => $id, 'isActive' => 0]);

//        Pokus deaktivovaneho pouzivatela o prihlasenie
        $response = $this->json('POST', '/api/login', ['email' => 'sally.smith@example.com', 'password' => 'Sally123!']);
        $response->assertStatus(403);

//        Spatna aktivacia pouzivatela s id 2
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer' . $token,
        ])->json('PATCH', '/api/users/2/activate');
        $response->assertStatus(200);
        $response->assertJson(['id' => $id, 'isActive' => 1]);

//        Pokus spatne aktivovaneho pouzivatela o prihlasenie
        $response = $this->json('POST', '/api/login', ['email' => 'sally.smith@example.com', 'password' => 'Sally123!']);
        $response->assertStatus(200);
    }

    public function testInvalidActivation(){
//        Pokus o deaktivaciu pri beznom pouzivatelovi
        $email = 'sally.smith@example.com';
        $password = 'Sally123!';
        $response = $this->json('POST', '/api/login', ['email' => $email, 'password' => $password]);
        $response->assertStatus(200);
        $token = $response->json()['data']['token'];
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer' . $token,
        ])->json('PATCH', '/api/users/4/deactivate');
        $response->assertStatus(401);
    }

    public function testValidForgottenPassword(){
//      Odoslanie vyresetovaneho hesla
        $email = 'sally.smith@example.com';
        $response = $this->json('POST', 'api/forgotten-password', ['email' => $email]);
        $response->assertStatus(200);
        $password = $response->content();
//      Prihlasenie pomocou noveho hesla
        $response = $this->json('POST', '/api/login', ['email' => $email, 'password' => $password]);
        $response->assertStatus(200);
//      Nastavenie povodneho hesla
        User::where('email',$email)->first()->update([
            "password" => Hash::make("Sally123!")
        ]);
    }

    public function testInvalidForgottenPassword(){
//      Pokus o vyresetovanie hesla na neexistujuceho pouzivatela
        $response = $this->json('POST', 'api/forgotten-password', ['email' => 'bad@email.com']);
        $response->assertStatus(409);
        $response->assertSee("Ziaden pouzivatel nie je zaregistrovany pod touto e-mailovou adresou.");

//      Vygenerovanie noveho hesla pouzivatelom
        $response = $this->json('POST', 'api/forgotten-password', ['email' => 'sally.smith@example.com']);
        $response->assertStatus(200);

//      Prihlasenie pomocou stareho hesla
        $response = $this->json('POST', '/api/login', ['email' => 'sally.smith@example.com', 'password' => 'Sally123!']);
        $response->assertStatus(401);

//      Nastavenie povodneho hesla
        User::where('email','sally.smith@example.com')->first()->update([
            "password" => Hash::make("Sally123!")
        ]);
    }
}

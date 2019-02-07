<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;

class RegistrationTest extends TestCase
{
    use DatabaseMigrations;
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testEndpoint()
    {
        $response = $this->get('/register');
        $response ->assertStatus(200);
    }

    public function testValid()
    {
        $response = $this->json('POST', '/register', ['name' => 'Sally','surname' => 'Smith','email' => 'sally.smith@gmail.com', 'password' => 'Sally123!', 'password_confirmation' => 'Sally123!']);
        $response
            ->assertStatus(302);
    }

    public function testNotValid()
    {
        $response = $this->json('POST', '/register', ['name' => 'Sally','surname' => 'Smith','email' => 'sallygmail.com', 'password' => 'Sally123', 'password_confirmation' => 'Sally123']);
        $response
            ->assertStatus(422);
    }
}

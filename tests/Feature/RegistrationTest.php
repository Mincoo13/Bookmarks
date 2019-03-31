<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Faker\Generator as Faker;

class RegistrationTest extends TestCase
{
    use WithFaker;
    //use DatabaseMigrations;
    /**
     * A basic test example.
     *
     * @return void
     */

    public function testValid()
    {

        $name = $this->faker->firstName();
        $surname = $this->faker->lastName();
        $email = $this->faker->unique()->safeEmail();
        $password='$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm';
        $response = $this->json('POST', 'api/register-user', ['name' => $name,'surname' => $surname,'email' => $email, 'password' => $password, 'password_confirmation' => $password]);
        $response
            ->assertStatus(201);
    }


    public function testNotValid()
    {
        $response = $this->json('POST', 'api/register-user', ['name' => 'Sally','surname' => 'Smith','email' => 'sallygmail.com', 'password' => 'Sally123', 'password_confirmation' => 'Sally123']);
        $response
            ->assertStatus(422);
    }
}

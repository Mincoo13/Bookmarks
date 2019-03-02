<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Illuminate\Support\Arr;


class LoginTest extends TestCase
{
    //use DatabaseMigrations;
    /**
     * A basic test example.
     *
     * @return void
     */
    public $token;

    public function testExample()
    {
        $this->assertTrue(true);
    }

    public function testValid()
    {
        $response = $this->json('POST', '/api/login', ['email' => 'sally.smith@example.com', 'password' => 'Sally123!']);
        $response
            ->assertStatus(200);
    }


    public function testNotValid()
    {
        $response = $this->json('POST', '/api/login', ['email' => 'bla@blaaa.com', 'password' => 'Ahoj1234!']);
        $response
            ->assertStatus(500);
    }
}

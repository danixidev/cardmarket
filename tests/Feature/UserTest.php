<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{
    public function test_noName()
    {
        $response = $this->put(
            '/api/login',
            [
                "username" => "",
                "password" => "Daniel1"
            ]
        );

        $response
            ->assertStatus(200)
            ->assertJson([
                'status' => 0,
            ]);

        // var_dump($response->original);
    }
    public function test_incorrectData()
    {
        $response = $this->put(
            '/api/login',
            [
                "username" => "daniel",
                "password" => "Password"
            ]
        );

        $response
            ->assertStatus(200)
            ->assertJson([
                'status' => 0,
            ]);

        // var_dump($response->original);
    }
    public function test_correctData()
    {
        $response = $this->put(
            '/api/login',
            [
                "username" => "dani",
                "password" => "Daniel1"
            ]
        );

        $response
            ->assertStatus(200)
            ->assertJson([
                'status' => 1,
            ]);

        // var_dump($response->original);
    }
}

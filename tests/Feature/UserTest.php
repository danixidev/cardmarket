<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{
    public function test_noName()
    {
        $data = [
            "username" => "",
            "password" => "Daniel1"
        ];

        $response = $this->putJson('/api/login', $data);

        $response
            ->assertStatus(200)
            ->assertJson([
                'status' => 0,
            ]);

        // var_dump($response->original);
    }
    public function test_incorrectData()
    {
        $data = [
            "username" => "daniel",
            "password" => "Password"
        ];

        $response = $this->putJson('/api/login', $data);

        $response
            ->assertStatus(200)
            ->assertJson([
                'status' => 0,
            ]);

        // var_dump($response->original);
    }
    public function test_correctData()
    {
        $data = [
            "username" => "dani",
            "password" => "Daniel1"
        ];

        $response = $this->putJson('/api/login', $data);

        $response
            ->assertStatus(200)
            ->assertJson([
                'status' => 1,
            ]);

        // var_dump($response->original);
    }
}

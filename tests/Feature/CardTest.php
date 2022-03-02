<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CardTest extends TestCase
{
    public function test_noApiToken()
    {
        $response = $this->post(
            '/api/cards/create',
            [
                "name" => "Mago de cristal",
                "description" => "Carta de mago con poderes de cristalizacion",
                "collection" => "1"
            ]
        )->parameters(
            [
                "api_token" => ""
            ]
        );

        $response
            ->assertStatus(200)
            ->assertJson([
                'status' => 0,
            ]);

        // var_dump($response->original);
    }
    public function test_noData()
    {
        $response = $this->post(
            '/api/cards/create',
            [
                "name" => "",
                "description" => "",
                "collection" => ""
            ]
        )->parameters(
            [
                "api_token" => "token"
            ]
        );

        $response
            ->assertStatus(200)
            ->assertJson([
                'status' => 0,
            ]);

        // var_dump($response->original);
    }
    public function test_incorrectCollection()
    {
        $response = $this->post(
            '/api/cards/create',
            [
                "name" => "Mago de cristal",
                "description" => "Carta de mago con poderes de cristalizacion",
                "collection" => "0"
            ]
        )->parameters(
            [
                "api_token" => "token"
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
        $response = $this->post(
            '/api/cards/create',
            [
                "name" => "Mago de cristal",
                "description" => "Carta de mago con poderes de cristalizacion",
                "collection" => "1"
            ]
        )->parameters(
            [
                "api_token" => "token"
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

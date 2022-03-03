<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CardTest extends TestCase
{
    public function test_noApiToken()
    {
        $data = [
            "name" => "Mago de cristal",
            "description" => "Carta de mago con poderes de cristalizacion",
            "collection_id" => "1"
        ];

        $headers = [
            "api-token" => ''
        ];

        $response = $this->postJson('/api/cards/create', $data, $headers);

        $response
            ->assertStatus(401);

        // var_dump($response->original);
    }
    public function test_noData()
    {
        $data = [
            "name" => "",
            "description" => "",
            "collection_id" => ""
        ];

        $headers = [
            "api-token" => '$2y$10$R48DO.Nuj/Ob4fxScB4bhO0wo8y0F7rHGqcsYHz/fAUX0DgmtVxX.'
        ];

        $response = $this->postJson('/api/cards/create', $data, $headers);

        $response
            ->assertStatus(200)
            ->assertJson([
                'status' => 0,
            ]);

        // var_dump($response->original);
    }
    public function test_incorrectCollection()
    {
        $data = [
            "name" => "Mago de cristal",
            "description" => "Carta de mago con poderes de cristalizacion",
            "collection_id" => "0"
        ];

        $headers = [
            "api-token" => '$2y$10$R48DO.Nuj/Ob4fxScB4bhO0wo8y0F7rHGqcsYHz/fAUX0DgmtVxX.'
        ];

        $response = $this->postJson('/api/cards/create', $data, $headers);

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
            "name" => "Mago de cristal",
            "description" => "Carta de mago con poderes de cristalizacion",
            "collection_id" => "1"
        ];

        $headers = [
            "api-token" => '$2y$10$R48DO.Nuj/Ob4fxScB4bhO0wo8y0F7rHGqcsYHz/fAUX0DgmtVxX.'
        ];

        $response = $this->postJson('/api/cards/create', $data, $headers);

        $response
            ->assertStatus(200)
            ->assertJson([
                'status' => 1,
            ]);


        // var_dump($response->original);
    }
}

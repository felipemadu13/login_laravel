<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;


class UserTest extends TestCase
{
    // use RefreshDatabase;

    /**
     * A basic feature test example.
     */
    public function test_user_can_create_a_account(): void
    {

        $response = $this->post('/api/user/cadastro', [
            'firstName' => fake()->firstName(),
            'lastName' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'cpf' => fake()->unique()->numerify('###########'),
            'phone' => fake()->numerify('##99#######'),
            'password' => bcrypt('roma'),
        ]);

        $response->assertStatus(201);
    }

    public function test_user_can_login(): void
    {
       $user = $this->post('/api/user/cadastro', [
            'firstName' => fake()->firstName(),
            'lastName' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'cpf' => fake()->unique()->numerify('###########'),
            'phone' => fake()->numerify('##99#######'),
            'password' => fake()->password(),
        ]);


        $response = $this->post('/api/login', [
            'email' => $user->json('email'),
            'password' => $user->json('password')
        ]);

        $response->assertStatus(200);
    }

}

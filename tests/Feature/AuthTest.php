<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Date;
use Tests\TestCase;

class AuthTest extends TestCase
{

    use RefreshDatabase;

    public function test_user_can_login(): void
    {

        $user = User::factory()->create();

        $this->assertNotNull($user);

        $response = $this->post('/api/login', [
            'email' => $user->email,
            'password' => 'foo'
        ]);

        $this->assertAuthenticated();

        $response->assertStatus(200);
        $response->assertJson(['token' => $response->json('token')]);
    }

    public function test_user_status_false_should_not_login()
    {
        $user = User::factory()->create(['status' => false]);

        $this->assertNotNull($user);

        $response = $this->post('/api/login', [
            'email' => $user->email,
            'password' => 'foo'
        ]);

        $this->assertGuest();

        $response->assertStatus(403);
        $response->assertJson(['error' => 'Usuário inativo ou inexistente']);
    }

    public function test_nonexistent_user_should_not_login()
    {
        $user = User::factory()->create();

        $this->assertNotNull($user);

        $response = $this->post('/api/login', [
            'email' => 'invalidemail@gmail.com',
            'password' => 'invalidpass'
        ]);

        $this->assertGuest();

        $response->assertStatus(403);
        $response->assertJson(['error' => 'Usuário inativo ou inexistente']);
    }

    public function test_user_with_wrong_email_should_not_login()
    {
        $user = User::factory()->create();

        $this->assertNotNull($user);

        $response = $this->post('/api/login', [
            'email' => 'invalidemail@gmail.com',
            'password' => 'foo'
        ]);

        $this->assertGuest();

        $response->assertStatus(403);
        $response->assertJson(['error' => 'Usuário inativo ou inexistente']);

    }

    public function test_deleted_user_should_not_login()
    {
        $admin = User::factory()->create([
            'email_verified_at' => Date::now(),
            'type' => 'admin'
        ]);
        $user = User::factory()->create(['email_verified_at' => Date::now()]);

        $this->assertNotNull($admin);
        $this->assertNotNull($user);

        $token = $this->post('/api/login', [
            'email' => $admin->email,
            'password' => 'foo'
        ])->json('token');

        $this->assertAuthenticatedAs($admin);

        $this->withHeaders([
            'Authorization' => 'Bearer' . $token,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ])->json('delete', '/api/v1/user/deletar/' . $user->id);

        $this->withHeaders([
            'Authorization' => 'Bearer' . $token,
        ])->post('api/v1/logout');

        $response = $this->post('/api/login', [
            'email' => $user->email,
            'password' => 'foo'
        ]);

        $this->assertGuest();

        $response->assertStatus(403);
        $response->assertJson(['error' => 'Usuário inativo ou inexistente']);

    }



    public function test_auth_user_can_do_function_me()
    {
        $user = User::factory()->create();

        $this->assertNotNull($user);

        $token = $this->post('/api/login', [
            'email' => $user->email,
            'password' => 'foo'
        ])->json('token');

        $this->assertAuthenticated();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer' . $token,
        ])->post('api/v1/me');

        $response->assertStatus(200);
    }

    public function test_auth_user_can_logout()
    {
        $user = User::factory()->create();

        $this->assertNotNull($user);

        $token = $this->post('/api/login', [
            'email' => $user->email,
            'password' => 'foo'
        ])->json('token');

        $this->assertAuthenticated();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer' . $token,
        ])->post('api/v1/logout');

        $response->assertStatus(200);
        $response->assertJson(['success' => 'Sessão encerrada com sucesso']);

    }

    public function test_auth_user_can_refresh_token()
    {
        $user = User::factory()->create();

        $this->assertNotNull($user);

        $token = $this->post('/api/login', [
            'email' => $user->email,
            'password' => 'foo'
        ])->json('token');

        $this->assertAuthenticated();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer' . $token,
        ])->post('api/v1/refresh');

        $response->assertStatus(201);
        $response->assertJsonStructure(['Novo Token']);
    }

}

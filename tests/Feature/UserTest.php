<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Date;
use Tests\TestCase;


class UserTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */
    public function test_user_can_create_a_account(): void
    {

        $response = $this->json('POST', '/api/user/cadastro', [
            'firstName' => fake()->firstName(),
            'lastName' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'cpf' => fake()->unique()->numerify('###########'),
            'phone' => fake()->numerify('##99#######'),
            'password' => fake()->password(),
        ]);

        $response->assertStatus(201);
    }

    public function test_user_can_login(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/api/login', [
            'email' => $user->email,
            'password' => 'foo'
        ]);


        $response->assertStatus(200);
    }

    public function test_auth_user_can_do_function_me()
    {
        $user = User::factory()->create();

        $login = $this->post('/api/login', [
            'email' => $user->email,
            'password' => 'foo'
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer' . $login->json('token'),
        ])->post('api/v1/me');

        $response->assertStatus(200);
    }

    public function test_auth_user_can_logout()
    {
        $user = User::factory()->create();

        $login = $this->post('/api/login', [
            'email' => $user->email,
            'password' => 'foo'
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer' . $login->json('token'),
        ])->post('api/v1/logout');

        $response->assertStatus(200);

    }

    public function test_auth_user_can_refresh_token()
    {
        $user = User::factory()->create();

        $login = $this->post('/api/login', [
            'email' => $user->email,
            'password' => 'foo'
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer' . $login->json('token'),
        ])->post('api/v1/refresh');

        $response->assertStatus(201);
    }

    public function test_auth_user_can_get_all_users()
    {
        $user = User::factory()->create(['email_verified_at' => Date::now()]);

        $login = $this->post('/api/login', [
            'email' => $user->email,
            'password' => 'foo'
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer' . $login->json('token'),
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ])->get('/api/v1/user/pegar-todos');

        $response->assertStatus(200);
    }

    public function test_auth_admin_can_get_one_user()
    {

        $admin = User::factory()->create([
            'email_verified_at' => Date::now(),
            'type' => 'admin']
        );
        $user = User::factory()->create(['email_verified_at' => Date::now()]);

        $login = $this->post('/api/login', [
            'email' => $admin->email,
            'password' => 'foo'
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer' . $login->json('token'),
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ])->get('/api/v1/user/pegar-um/' . $user->id);

        $response->assertStatus(200);
    }

    public function test_auth_user_can_get_himself()
    {

        $user = User::factory()->create(['email_verified_at' => Date::now()]);

        $login = $this->post('/api/login', [
            'email' => $user->email,
            'password' => 'foo'
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer' . $login->json('token'),
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ])->get('/api/v1/user/pegar-um/' . $user->id);

        $response->assertStatus(200);
    }

    public function test_auth_admin_can_put_user()
    {
        $admin = User::factory()->create([
            'email_verified_at' => Date::now(),
            'type' => 'admin']
        );
        $user = User::factory()->create(['email_verified_at' => Date::now()]);

        $login = $this->post('/api/login', [
            'email' => $admin->email,
            'password' => 'foo'
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer' . $login->json('token'),
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ])->json('PUT', '/api/v1/user/atualizar/' . $user->id, [
            'firstName' => fake()->firstName(),
            'lastName' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'cpf' => fake()->unique()->numerify('###########'),
            'phone' => fake()->numerify('##99#######'),
            'type' => 'user'
        ]);

        $response->assertStatus(200);
    }

    public function test_auth_user_can_put_himself()
    {
        $user = User::factory()->create(['email_verified_at' => Date::now()]);

        $login = $this->post('/api/login', [
            'email' => $user->email,
            'password' => 'foo'
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer' . $login->json('token'),
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ])->json('PUT', '/api/v1/user/atualizar/' . $user->id, [
            'firstName' => fake()->firstName(),
            'lastName' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'cpf' => fake()->unique()->numerify('###########'),
            'phone' => fake()->numerify('##99#######'),
            'type' => 'user'
        ]);

        $response->assertStatus(200);
    }

    public function test_auth_admin_can_patch_user_password()
    {
        $admin = User::factory()->create([
            'email_verified_at' => Date::now(),
            'type' => 'admin']
        );
        $user = User::factory()->create(['email_verified_at' => Date::now()]);

        $login = $this->post('/api/login', [
            'email' => $admin->email,
            'password' => 'foo'
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer' . $login->json('token'),
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ])->json('PATCH', '/api/v1/user/atualizar-senha/' . $user->id, [
            'password' => fake()->password()
        ]);

        $response->assertStatus(200);
    }


    public function test_auth_user_can_patch_password_himself()
    {
        $user = User::factory()->create(['email_verified_at' => Date::now()]);

        $login = $this->post('/api/login', [
            'email' => $user->email,
            'password' => 'foo'
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer' . $login->json('token'),
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ])->json('PATCH', '/api/v1/user/atualizar-senha/' . $user->id, [
            'password' => fake()->password()
        ]);

        $response->assertStatus(200);
    }

    public function test_auth_admin_can_delete_user()
    {
        $admin = User::factory()->create([
            'email_verified_at' => Date::now(),
            'type' => 'admin']
        );
        $user = User::factory()->create(['email_verified_at' => Date::now()]);

        $login = $this->post('/api/login', [
            'email' => $admin->email,
            'password' => 'foo'
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer' . $login->json('token'),
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ])->json('delete', '/api/v1/user/deletar/' . $user->id);

        $response->assertStatus(200);

    }

    public function test_auth_user_can_delete_himself()
    {
        $user = User::factory()->create(['email_verified_at' => Date::now()]);

        $login = $this->post('/api/login', [
            'email' => $user->email,
            'password' => 'foo'
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer' . $login->json('token'),
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ])->json('delete', '/api/v1/user/deletar/' . $user->id);

        $response->assertStatus(200);

    }

    public function test_auth_admin_can_create_admin()
    {
        $admin = User::factory()->create([
            'email_verified_at' => Date::now(),
            'type' => 'admin']
        );

        $login = $this->post('/api/login', [
            'email' => $admin->email,
            'password' => 'foo'
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer' . $login->json('token'),
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ])->json('post', '/api/v1/user/cadastro/admin', [
            'firstName' => fake()->firstName(),
            'lastName' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'cpf' => fake()->unique()->numerify('###########'),
            'phone' => fake()->numerify('##99#######'),
            'password' => fake()->password(),
        ]);

        $response->assertStatus(201);

    }

    public function test_auth_admin_can_change_user_status()
    {
        $admin = User::factory()->create([
            'email_verified_at' => Date::now(),
            'type' => 'admin']
        );
        $user = User::factory()->create(['email_verified_at' => Date::now()]);

        $login = $this->post('/api/login', [
            'email' => $admin->email,
            'password' => 'foo'
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer' . $login->json('token'),
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ])->json('PATCH', '/api/v1/user/mudar-status/' . $user->id, [
            'status' => false
        ]);

        $response->assertStatus(200);

    }




}

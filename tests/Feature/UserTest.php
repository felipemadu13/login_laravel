<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;


class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_a_account()
    {
        $data = [
            'firstName' => fake()->firstName(),
            'lastName' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'cpf' => fake()->unique()->numerify('###########'),
            'phone' => fake()->numerify('##99#######'),
            'password' => fake()->password(),
        ];

        $response = $this->json('POST', '/api/user/cadastro', $data);

        $this->assertDatabaseHas('users', [
            'firstName' => $data['firstName'],
            'lastName' => $data['lastName'],
            'email' => $data['email'],
            'cpf' => $data['cpf'],
            'phone' => $data['phone'],
        ]);

        $response->assertStatus(201);

    }

    public function test_user_store_firstName_required()
    {
        $data = [
            'lastName' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'cpf' => fake()->unique()->numerify('###########'),
            'phone' => fake()->numerify('##99#######'),
            'password' => fake()->password(),
        ];

        $response = $this->json('POST', '/api/user/cadastro', $data);

        $response->assertJson(['message' => 'O campo first name é o obrigatório.']);
        $response->assertStatus(422);
    }

    public function test_user_store_lastName_required()
    {
        $data = [
            'firstName' => fake()->firstName(),
            'email' => fake()->unique()->safeEmail(),
            'cpf' => fake()->unique()->numerify('###########'),
            'phone' => fake()->numerify('##99#######'),
            'password' => fake()->password(),
        ];

        $response = $this->json('POST', '/api/user/cadastro', $data);

        $response->assertJson(['message' => 'O campo last name é o obrigatório.']);
        $response->assertStatus(422);
    }

    public function test_user_store_email_required()
    {
        $data = [
            'firstName' => fake()->firstName(),
            'lastName' => fake()->lastName(),
            'cpf' => fake()->unique()->numerify('###########'),
            'phone' => fake()->numerify('##99#######'),
            'password' => fake()->password(),
        ];

        $response = $this->json('POST', '/api/user/cadastro', $data);
        $response->assertJson(['message' => 'O campo email é o obrigatório.']);
        $response->assertStatus(422);
    }

    public function test_user_store_email_unique()
    {
        $dataUser1 = [
            'firstName' => fake()->firstName(),
            'lastName' => fake()->lastName(),
            'email' => 'emaildetest@test.com',
            'cpf' => fake()->unique()->numerify('###########'),
            'phone' => fake()->numerify('##99#######'),
            'password' => fake()->password(),
        ];

        $dataUser2 = [
            'firstName' => fake()->firstName(),
            'lastName' => fake()->lastName(),
            'email' => 'emaildetest@test.com',
            'cpf' => fake()->unique()->numerify('###########'),
            'phone' => fake()->numerify('##99#######'),
            'password' => fake()->password(),
        ];

        $responseUser1 = $this->json('POST', '/api/user/cadastro', $dataUser1);
        $responseUser2 = $this->json('POST', '/api/user/cadastro', $dataUser2);

        $responseUser2->assertJson(['message' => 'E-mail já cadastrado no sistema.']);
        $responseUser2->assertStatus(422);
    }

    public function test_user_store_email_safe_email()
    {
        $data = [
            'firstName' => fake()->firstName(),
            'lastName' => fake()->lastName(),
            'email' => 'email.sem.arroba.teste.com',
            'cpf' => fake()->unique()->numerify('###########'),
            'phone' => fake()->numerify('##99#######'),
            'password' => fake()->password(),
        ];

        $response = $this->json('POST', '/api/user/cadastro', $data);

        $response->assertJson(['message' => 'O campo e-mail deve ser um endereço válido.']);
        $response->assertStatus(422);
    }

    public function test_user_store_cpf_required()
    {
        $data = [
            'firstName' => fake()->firstName(),
            'lastName' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->numerify('##99#######'),
            'password' => fake()->password(),
        ];

        $response = $this->json('POST', '/api/user/cadastro', $data);

        $response->assertJson(['message' => 'O campo cpf é o obrigatório.']);
        $response->assertStatus(422);
    }

    public function test_user_store_cpf_unique()
    {
        $dataUser1 = [
            'firstName' => fake()->firstName(),
            'lastName' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'cpf' => '00011122233',
            'phone' => fake()->numerify('##99#######'),
            'password' => fake()->password(),
        ];

        $dataUser2 = [
            'firstName' => fake()->firstName(),
            'lastName' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'cpf' => '00011122233',
            'phone' => fake()->numerify('##99#######'),
            'password' => fake()->password(),
        ];

        $responseUser1 = $this->json('POST', '/api/user/cadastro', $dataUser1);
        $responseUser2 = $this->json('POST', '/api/user/cadastro', $dataUser2);

        $responseUser2->assertJson(['message' => 'CPF já cadastrado no sistema.']);
        $responseUser2->assertStatus(422);
    }

    public function test_user_store_cpf_have_to_be_11_digits()
    {
        $data = [
            'firstName' => fake()->firstName(),
            'lastName' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'cpf' => '123456789',
            'phone' => fake()->numerify('##99#######'),
            'password' => fake()->password(),
        ];

        $response = $this->json('POST', '/api/user/cadastro', $data);

        $response->assertJson(['message' => 'CPF digitado incorretamente.']);
        $response->assertStatus(422);
    }

    public function test_user_store_phone_required()
    {
        $data = [
            'firstName' => fake()->firstName(),
            'lastName' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'cpf' => fake()->unique()->numerify('###########'),
            'password' => fake()->password(),
        ];

        $response = $this->json('POST', '/api/user/cadastro', $data);

        $response->assertJson(['message' => 'O campo phone é o obrigatório.']);
        $response->assertStatus(422);
    }

    public function test_user_store_password_required()
    {
        $data = [
            'firstName' => fake()->firstName(),
            'lastName' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'cpf' => fake()->unique()->numerify('###########'),
            'phone' => fake()->numerify('##99#######')
        ];

        $response = $this->json('POST', '/api/user/cadastro', $data);

        $response->assertJson(['message' => 'O campo password é o obrigatório.']);
        $response->assertStatus(422);
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

    public function test_user_status_false_should_not_login()
    {
        $user = User::factory()->create(['status' => false]);

        $response = $this->post('/api/login', [
            'email' => $user->email,
            'password' => 'foo'
        ]);

        $response->assertStatus(403);
    }

    public function test_nonexistent_user_should_not_login()
    {
        $user = User::factory()->create();

        $response = $this->post('/api/login', [
            'email' => 'invalidemail@gmail.com',
            'password' => 'invalidpass'
        ]);

        $response->assertStatus(403);
    }

    public function test_user_with_wrong_email_should_not_login()
    {
        $user = User::factory()->create();

        $response = $this->post('/api/login', [
            'email' => 'invalidemail@gmail.com',
            'password' => 'foo'
        ]);

        $response->assertStatus(403);

    }

    public function test_deleted_user_should_not_login()
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

        $this->withHeaders([
            'Authorization' => 'Bearer' . $login->json('token'),
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ])->json('delete', '/api/v1/user/deletar/' . $user->id);

        $response = $this->post('/api/login', [
            'email' => $user->email,
            'password' => 'foo'
        ]);

        $response->assertStatus(403);

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




    public function test_auth_admin_can_get_user()
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

    public function test_unauthorized_user_should_not_get_other_user()
    {
        $user1 = User::factory()->create([
            'email_verified_at' => Date::now(),
            'type' => 'user'
        ]);
        $user2 = User::factory()->create([
            'email_verified_at' => Date::now(),
            'type' => 'user'
        ]);

        $login = $this->post('/api/login', [
            'email' => $user1->email,
            'password' => 'foo'
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer' . $login->json('token'),
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ])->get('/api/v1/user/pegar-um/' . $user2->id);

        $response->assertStatus(403);

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

    public function test_auth_user_put_firstName_required()
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
            'lastName' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'cpf' => fake()->unique()->numerify('###########'),
            'phone' => fake()->numerify('##99#######'),
            'type' => 'user'
        ]);

        $response->assertJson(['message' => 'O campo first name é o obrigatório.']);
        $response->assertStatus(422);

    }

    public function test_auth_user_put_lastName_required()
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
            'email' => fake()->unique()->safeEmail(),
            'cpf' => fake()->unique()->numerify('###########'),
            'phone' => fake()->numerify('##99#######'),
            'type' => 'user'
        ]);

        $response->assertJson(['message' => 'O campo last name é o obrigatório.']);
        $response->assertStatus(422);

    }

    public function test_auth_user_put_email_required()
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
            'cpf' => fake()->unique()->numerify('###########'),
            'phone' => fake()->numerify('##99#######'),
            'type' => 'user'
        ]);

        $response->assertJson(['message' => 'O campo email é o obrigatório.']);
        $response->assertStatus(422);

    }

    public function test_auth_user_put_email_unique()
    {
        $user = User::factory()->create([
            'email' => 'emaildetest@test.com',
            'email_verified_at' => Date::now()
        ]);
        $user2 = User::factory()->create(['email_verified_at' => Date::now()]);

        $token = $this->post('/api/login', [
            'email' => $user2->email,
            'password' => 'foo',
        ])->json('token');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer' . $token,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json']
            )->json('PUT', '/api/v1/user/atualizar/' . $user2->id, [
            'firstName' => fake()->firstName,
            'lastName' => fake()->lastName,
            'email' => 'emaildetest@test.com',
            'cpf' => fake()->unique()->numerify('###########'),
            'phone' => fake()->numerify('##99#######'),
            'type' => 'user',
        ]);

        $response->assertJson(['message' => 'E-mail já cadastrado no sistema.']);
        $response->assertStatus(422);


    }

    public function test_auth_user_put_email_safe_email()
    {
        $user = User::factory()->create(['email_verified_at' => Date::now()]);

        $token = $this->post('/api/login', [
            'email' => $user->email,
            'password' => 'foo',
        ])->json('token');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer' . $token,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json']
            )->json('PUT', '/api/v1/user/atualizar/' . $user->id, [
            'firstName' => fake()->firstName,
            'lastName' => fake()->lastName,
            'email' => 'emaildetesttest.com',
            'cpf' => fake()->unique()->numerify('###########'),
            'phone' => fake()->numerify('##99#######'),
            'type' => 'user',
        ]);

        $response->assertJson(['message' => 'O campo e-mail deve ser um endereço válido.']);
        $response->assertStatus(422);

    }

    public function test_auth_user_put_cpf_required()
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
            'lastName' => fake()->lastName(),
            'firstName' => fake()->firstName(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->numerify('##99#######'),
            'type' => 'user'
        ]);

        $response->assertJson(['message' => 'O campo cpf é o obrigatório.']);
        $response->assertStatus(422);

    }

    public function test_auth_user_put_cpf_unique()
    {
        $user1 = User::factory()->create([
            'email_verified_at' => Date::now(),
            'cpf' => '12345678911'
        ]);
        $user2 = User::factory()->create(['email_verified_at' => Date::now()]);

        $login = $this->post('/api/login', [
            'email' => $user2->email,
            'password' => 'foo'
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer' . $login->json('token'),
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ])->json('PUT', '/api/v1/user/atualizar/' . $user2->id, [
            'lastName' => fake()->lastName(),
            'firstName' => fake()->firstName(),
            'cpf' => '12345678911',
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->numerify('##99#######'),
            'type' => 'user'
        ]);

        $response->assertJson(['message' => 'CPF já cadastrado no sistema.']);
        $response->assertStatus(422);

    }

    public function test_auth_user_put_cpf_11_digits()
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
            'lastName' => fake()->lastName(),
            'firstName' => fake()->firstName(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->numerify('##99#######'),
            'cpf' => '123456789',
            'type' => 'user'
        ]);

        $response->assertJson(['message' => 'CPF digitado incorretamente.']);
        $response->assertStatus(422);


    }

    public function test_auth_user_put_phone_required()
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
            'lastName' => fake()->lastName(),
            'firstName' => fake()->firstName(),
            'email' => fake()->unique()->safeEmail(),
            'cpf' => fake()->unique()->numerify('###########'),
            'type' => 'user'
        ]);

        $response->assertJson(['message' => 'O campo phone é o obrigatório.']);
        $response->assertStatus(422);

    }

    public function test_auth_user_put_type_required()
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
            'lastName' => fake()->lastName(),
            'firstName' => fake()->firstName(),
            'email' => fake()->unique()->safeEmail(),
            'cpf' => fake()->unique()->numerify('###########'),
            'phone' => fake()->numerify('##99#######'),
        ]);

        $response->assertJson(['message' => 'O campo type é o obrigatório.']);
        $response->assertStatus(422);

    }





    public function test_unauthorized_user_should_not_put_another_user()
    {
        $user1 = User::factory()->create([
            'email_verified_at' => Date::now(),
            'type' => 'user'
        ]);
        $user2 = User::factory()->create([
            'email_verified_at' => Date::now(),
            'type' => 'user'
        ]);

        $login = $this->post('/api/login', [
            'email' => $user1->email,
            'password' => 'foo'
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer' . $login->json('token'),
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ])->json('PUT', '/api/v1/user/atualizar/' . $user2->id, [
            'firstName' => fake()->firstName(),
            'lastName' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'cpf' => fake()->unique()->numerify('###########'),
            'phone' => fake()->numerify('##99#######'),
            'type' => 'user'
        ]);

        $response->assertStatus(403);

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

        $this->assertSoftDeleted($user);
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

        $this->assertSoftDeleted($user);
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

    public function test_forgot_password_send_email_to_user()
    {
        $user = User::factory()->create(['email_verified_at' => Date::now()]);

        $response = $this->json('POST', '/api/forgot-password/email-recuperacao', [
            'email' => $user->email
        ]);

        $response->assertStatus(200);

    }

    public function test_forgot_password_user_get_new_password()
    {
        $user = User::factory()->create(['email_verified_at' => Date::now()]);

        $token = Password::createToken($user);

        $response = $this->json('PUT', '/api/forgot-password/nova-senha', [
            'email' => $user->email,
            'password' => 'foo',
            'password_confirmation' => 'foo',
            'token' => $token,
        ]);

        $response->assertStatus(200);

    }

    public function test_auth_user_can_send_email_verification()
    {
        $user = User::factory()->create();

        $login = $this->post('/api/login', [
            'email' => $user->email,
            'password' => 'foo'
        ]);

        Notification::fake();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer' . $login->json('token'),
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ])->json('POST', '/api/v1/email-verificacao');

        $response->assertStatus(200);
        Notification::assertSentTo($user, VerifyEmail::class);

    }

    public function test_auth_user_verified_should_not_send_email_verification()
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
        ])->json('POST', '/api/v1/email-verificacao');

        $response->assertStatus(200)
                 ->assertJson(['message' => 'E-mail já verificado']);

    }




}

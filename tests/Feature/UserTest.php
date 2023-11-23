<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Date;
use Tests\TestCase;


class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_a_account()
    {
        $user = User::factory()->make();

        $this->assertNotNull($user);
        $this->assertIsArray($user->toArray());

        $response = $this->json('POST', '/api/user/cadastro', $user->toArray());

        $this->assertDatabaseHas('users', [
            'firstName' => $user['firstName'],
            'lastName' => $user['lastName'],
            'email' => $user['email'],
            'cpf' => $user['cpf'],
            'phone' => $user['phone'],
        ]);

        $response->assertStatus(201);
        $response->assertJsonStructure(['success']);
    }


    public function test_user_store_firstName_required()
    {
        $user = User::factory()->make(['firstName' => null]);

        $this->assertNotNull($user);
        $this->assertIsArray($user->toArray());

        $response = $this->json('POST', '/api/user/cadastro', $user->toArray());

        $response->assertInvalid(['firstName']);
        $response->assertJson(['message' => 'O campo first name é o obrigatório.']);
        $response->assertStatus(422);
    }

    public function test_user_store_lastName_required()
    {
        $user = User::factory()->make(['lastName' => null]);

        $this->assertNotNull($user);
        $this->assertIsArray($user->toArray());

        $response = $this->json('POST', '/api/user/cadastro', $user->toArray());

        $response->assertInvalid(['lastName']);
        $response->assertJson(['message' => 'O campo last name é o obrigatório.']);
        $response->assertStatus(422);
    }

    public function test_user_store_email_required()
    {
        $user = User::factory()->make(['email' => null]);

        $this->assertNotNull($user);
        $this->assertIsArray($user->toArray());

        $response = $this->json('POST', '/api/user/cadastro', $user->toArray());

        $response->assertInvalid(['email']);
        $response->assertJson(['message' => 'O campo email é o obrigatório.']);
        $response->assertStatus(422);
    }

    public function test_user_store_email_unique()
    {
        $user1 = User::factory()->make(['email' => 'email@test.com']);
        $user2 = User::factory()->make(['email' => 'email@test.com']);

        $this->assertNotNull($user1);
        $this->assertIsArray($user1->toArray());

        $this->assertNotNull($user2);
        $this->assertIsArray($user2->toArray());

        $this->json('POST', '/api/user/cadastro', $user1->toArray());
        $response = $this->json('POST', '/api/user/cadastro', $user2->toArray());

        $response->assertInvalid(['email']);
        $response->assertJson(['message' => 'E-mail já cadastrado no sistema.']);
        $response->assertStatus(422);
    }

    public function test_user_store_email_safe_email()
    {
        $user = User::factory()->make(['email' => 'email.test.com']);

        $this->assertNotNull($user);
        $this->assertIsArray($user->toArray());

        $response = $this->json('POST', '/api/user/cadastro', $user->toArray());

        $response->assertInvalid(['email']);
        $response->assertJson(['message' => 'O campo e-mail deve ser um endereço válido.']);
        $response->assertStatus(422);
    }

    public function test_user_store_cpf_required()
    {
        $user = User::factory()->make(['cpf' => null]);

        $this->assertNotNull($user);
        $this->assertIsArray($user->toArray());

        $response = $this->json('POST', '/api/user/cadastro', $user->toArray());

        $response->assertInvalid(['cpf']);
        $response->assertJson(['message' => 'O campo cpf é o obrigatório.']);
        $response->assertStatus(422);
    }

    public function test_user_store_cpf_unique()
    {
        $user1 = User::factory()->make(['cpf' => '00011122233']);
        $user2 = User::factory()->make(['cpf' => '00011122233']);

        $this->assertNotNull($user1);
        $this->assertIsArray($user1->toArray());

        $this->assertNotNull($user2);
        $this->assertIsArray($user2->toArray());

        $this->json('POST', '/api/user/cadastro', $user1->toArray());
        $response = $this->json('POST', '/api/user/cadastro', $user2->toArray());

        $response->assertInvalid(['cpf']);
        $response->assertJson(['message' => 'CPF já cadastrado no sistema.']);
        $response->assertStatus(422);
    }

    public function test_user_store_cpf_have_to_be_11_digits()
    {
        $user = User::factory()->make(['cpf' => '123456789']);

        $this->assertNotNull($user);
        $this->assertIsArray($user->toArray());

        $response = $this->json('POST', '/api/user/cadastro', $user->toArray());

        $response->assertInvalid(['cpf']);
        $response->assertJson(['message' => 'CPF digitado incorretamente.']);
        $response->assertStatus(422);
    }

    public function test_user_store_phone_required()
    {
        $user = User::factory()->make(['phone' => null]);

        $this->assertNotNull($user);
        $this->assertIsArray($user->toArray());

        $response = $this->json('POST', '/api/user/cadastro', $user->toArray());

        $response->assertInvalid(['phone']);
        $response->assertJson(['message' => 'O campo phone é o obrigatório.']);
        $response->assertStatus(422);
    }

    public function test_user_store_password_required()
    {
        $user = User::factory()->make(['password' => null]);

        $this->assertNotNull($user);
        $this->assertIsArray($user->toArray());

        $response = $this->json('POST', '/api/user/cadastro', $user->toArray());

        $response->assertInvalid(['password']);
        $response->assertJson(['message' => 'O campo password é o obrigatório.']);
        $response->assertStatus(422);
    }


    public function test_auth_user_can_get_just_himself_in_find_all()
    {
        $user = User::factory()->create(['email_verified_at' => Date::now()]);

        $this->assertNotNull($user);

        $token = $this->post('/api/login', [
            'email' => $user->email,
            'password' => 'foo'
        ])->json('token');

        $this->assertAuthenticatedAs($user);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer' . $token,
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

        $this->assertNotNull($admin);
        $this->assertNotNull($user);

        $token = $this->post('/api/login', [
            'email' => $admin->email,
            'password' => 'foo'
        ])->json('token');

        $this->assertAuthenticatedAs($admin);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer' . $token,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ])->get('/api/v1/user/pegar-um/' . $user->id);

        $response->assertStatus(200);
    }

    public function test_auth_user_can_get_himself()
    {

        $user = User::factory()->create(['email_verified_at' => Date::now()]);

        $this->assertNotNull($user);

        $token = $this->post('/api/login', [
            'email' => $user->email,
            'password' => 'foo'
        ])->json('token');

        $this->assertAuthenticatedAs($user);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer' . $token,
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

        $this->assertNotNull($user1);
        $this->assertNotNull($user2);

        $token = $this->post('/api/login', [
            'email' => $user1->email,
            'password' => 'foo'
        ])->json('token');

        $this->assertAuthenticatedAs($user1);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer' . $token,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ])->get('/api/v1/user/pegar-um/' . $user2->id);

        $response->assertStatus(403);
        $response->assertJson(['error' => 'Usuário não autorizado.']);
    }

    public function test_auth_admin_can_put_user()
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

        $data = User::factory()->make();

        $this->assertNotNull($data);
        $this->assertIsArray($data->toArray());

        $response = $this->withHeaders([
            'Authorization' => 'Bearer' . $token,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ])->json('PUT', '/api/v1/user/atualizar/' . $user->id, $data->toArray());

        $response->assertStatus(200);
    }

    public function test_auth_user_can_put_himself()
    {
        $user = User::factory()->create(['email_verified_at' => Date::now()]);

        $this->assertNotNull($user);

        $token = $this->post('/api/login', [
            'email' => $user->email,
            'password' => 'foo'
        ])->json('token');

        $this->assertAuthenticatedAs($user);

        $data = User::factory()->make();

        $this->assertNotNull($data);
        $this->assertIsArray($data->toArray());

        $response = $this->withHeaders([
            'Authorization' => 'Bearer' . $token,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ])->json('PUT', '/api/v1/user/atualizar/' . $user->id, $data->toArray());

        $response->assertStatus(200);
    }

    public function test_auth_user_put_firstName_required()
    {
        $user = User::factory()->create(['email_verified_at' => Date::now()]);

        $this->assertNotNull($user);

        $token = $this->post('/api/login', [
            'email' => $user->email,
            'password' => 'foo'
        ])->json('token');

        $this->assertAuthenticatedAs($user);

        $data = User::factory()->make(['firstName' => null]);

        $this->assertNotNull($data);
        $this->assertIsArray($data->toArray());

        $response = $this->withHeaders([
            'Authorization' => 'Bearer' . $token,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ])->json('PUT', '/api/v1/user/atualizar/' . $user->id, $data->toArray());

        $response->assertInvalid(['firstName']);
        $response->assertJson(['message' => 'O campo first name é o obrigatório.']);
        $response->assertStatus(422);

    }

    public function test_auth_user_put_lastName_required()
    {
        $user = User::factory()->create(['email_verified_at' => Date::now()]);

        $this->assertNotNull($user);

        $token = $this->post('/api/login', [
            'email' => $user->email,
            'password' => 'foo'
        ])->json('token');

        $this->assertAuthenticatedAs($user);

        $data = User::factory()->make(['lastName' => null]);

        $this->assertNotNull($data);
        $this->assertIsArray($data->toArray());

        $response = $this->withHeaders([
            'Authorization' => 'Bearer' . $token,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ])->json('PUT', '/api/v1/user/atualizar/' . $user->id, $data->toArray());

        $response->assertInvalid(['lastName']);
        $response->assertJson(['message' => 'O campo last name é o obrigatório.']);
        $response->assertStatus(422);

    }

    public function test_auth_user_put_email_required()
    {
        $user = User::factory()->create(['email_verified_at' => Date::now()]);

        $this->assertNotNull($user);

        $token = $this->post('/api/login', [
            'email' => $user->email,
            'password' => 'foo'
        ])->json('token');

        $this->assertAuthenticatedAs($user);

        $data = User::factory()->make(['email' => null]);

        $this->assertNotNull($data);
        $this->assertIsArray($data->toArray());

        $response = $this->withHeaders([
            'Authorization' => 'Bearer' . $token,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ])->json('PUT', '/api/v1/user/atualizar/' . $user->id, $data->toArray());

        $response->assertInvalid(['email']);
        $response->assertJson(['message' => 'O campo email é o obrigatório.']);
        $response->assertStatus(422);

    }

    public function test_auth_user_put_email_unique()
    {
        $user1 = User::factory()->create([
            'email' => 'emaildetest@test.com',
            'email_verified_at' => Date::now()
        ]);
        $user2 = User::factory()->create(['email_verified_at' => Date::now()]);

        $this->assertNotNull($user1);
        $this->assertNotNull($user2);

        $token = $this->post('/api/login', [
            'email' => $user2->email,
            'password' => 'foo',
        ])->json('token');

        $this->assertAuthenticatedAs($user2);

        $data = User::factory()->make(['email' => 'emaildetest@test.com']);
        $this->assertNotNull($data);
        $this->assertIsArray($data->toArray());

        $response = $this->withHeaders([
            'Authorization' => 'Bearer' . $token,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ])->json('PUT', '/api/v1/user/atualizar/' . $user2->id, $data->toArray());

        $response->assertJson(['message' => 'E-mail já cadastrado no sistema.']);
        $response->assertStatus(422);


    }

    public function test_auth_user_put_email_safe_email()
    {
        $user = User::factory()->create(['email_verified_at' => Date::now()]);

        $this->assertNotNull($user);

        $token = $this->post('/api/login', [
            'email' => $user->email,
            'password' => 'foo',
        ])->json('token');

        $this->assertAuthenticatedAs($user);

        $data = User::factory()->make(['email' => 'email.test.com']);

        $this->assertNotNull($data);
        $this->assertIsArray($data->toArray());

        $response = $this->withHeaders([
            'Authorization' => 'Bearer' . $token,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ])->json('PUT', '/api/v1/user/atualizar/' . $user->id, $data->toArray());

        $response->assertJson(['message' => 'O campo e-mail deve ser um endereço válido.']);
        $response->assertStatus(422);

    }

    public function test_auth_user_put_cpf_required()
    {
        $user = User::factory()->create(['email_verified_at' => Date::now()]);

        $this->assertNotNull($user);

        $token = $this->post('/api/login', [
            'email' => $user->email,
            'password' => 'foo'
        ])->json('token');

        $this->assertAuthenticatedAs($user);

        $data = User::factory()->make(['cpf' => null]);

        $this->assertNotNull($data);
        $this->assertIsArray($data->toArray());

        $response = $this->withHeaders([
            'Authorization' => 'Bearer' . $token,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ])->json('PUT', '/api/v1/user/atualizar/' . $user->id, $data->toArray());

        $response->assertInvalid(['cpf']);
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

        $this->assertNotNull($user1);
        $this->assertNotNull($user2);

        $token = $this->post('/api/login', [
            'email' => $user2->email,
            'password' => 'foo'
        ])->json('token');

        $this->assertAuthenticatedAs($user2);

        $data = User::factory()->make(['cpf' => '12345678911']);

        $this->assertNotNull($data);
        $this->assertIsArray($data->toArray());

        $response = $this->withHeaders([
            'Authorization' => 'Bearer' . $token,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ])->json('PUT', '/api/v1/user/atualizar/' . $user2->id, $data->toArray());

        $response->assertJson(['message' => 'CPF já cadastrado no sistema.']);
        $response->assertStatus(422);

    }

    public function test_auth_user_put_cpf_11_digits()
    {
        $user = User::factory()->create(['email_verified_at' => Date::now()]);

        $this->assertNotNull($user);

        $token = $this->post('/api/login', [
            'email' => $user->email,
            'password' => 'foo'
        ])->json('token');

        $this->assertAuthenticatedAs($user);

        $data = User::factory()->make(['cpf' => '123456789']);

        $this->assertNotNull($data);
        $this->assertIsArray($data->toArray());

        $response = $this->withHeaders([
            'Authorization' => 'Bearer' . $token,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ])->json('PUT', '/api/v1/user/atualizar/' . $user->id, $data->toArray());

        $response->assertJson(['message' => 'CPF digitado incorretamente.']);
        $response->assertStatus(422);


    }

    public function test_auth_user_put_phone_required()
    {
        $user = User::factory()->create(['email_verified_at' => Date::now()]);

        $this->assertNotNull($user);

        $token = $this->post('/api/login', [
            'email' => $user->email,
            'password' => 'foo'
        ])->json('token');

        $this->assertAuthenticatedAs($user);

        $data = User::factory()->make(['phone' => null]);

        $this->assertNotNull($data);
        $this->assertIsArray($data->toArray());

        $response = $this->withHeaders([
            'Authorization' => 'Bearer' . $token,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ])->json('PUT', '/api/v1/user/atualizar/' . $user->id, $data->toArray());

        $response->assertInvalid(['phone']);
        $response->assertJson(['message' => 'O campo phone é o obrigatório.']);
        $response->assertStatus(422);

    }

    public function test_auth_user_put_type_required()
    {
        $user = User::factory()->create(['email_verified_at' => Date::now()]);

        $this->assertNotNull($user);

        $token = $this->post('/api/login', [
            'email' => $user->email,
            'password' => 'foo'
        ])->json('token');

        $this->assertAuthenticatedAs($user);

        $data = User::factory()->make(['type' => null]);

        $this->assertNotNull($data);
        $this->assertIsArray($data->toArray());

        $response = $this->withHeaders([
            'Authorization' => 'Bearer' . $token,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ])->json('PUT', '/api/v1/user/atualizar/' . $user->id, $data->toArray());

        $response->assertInvalid(['type']);
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

        $this->assertNotNull($user1);
        $this->assertNotNull($user2);

        $token = $this->post('/api/login', [
            'email' => $user1->email,
            'password' => 'foo'
        ])->json('token');

        $this->assertAuthenticatedAs($user1);

        $data = User::factory()->make();

        $this->assertNotNull($data);
        $this->assertIsArray($data->toArray());

        $response = $this->withHeaders([
            'Authorization' => 'Bearer' . $token,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ])->json('PUT', '/api/v1/user/atualizar/' . $user2->id, $data->toArray());

        $response->assertStatus(403);
        $response->assertJson(['error' => 'Usuário não autorizado.']);

    }

    public function test_auth_admin_can_patch_user_password()
    {
        $admin = User::factory()->create([
            'email_verified_at' => Date::now(),
            'type' => 'admin']
        );
        $user = User::factory()->create(['email_verified_at' => Date::now()]);

        $this->assertNotNull($admin);
        $this->assertNotNull($user);

        $token = $this->post('/api/login', [
            'email' => $admin->email,
            'password' => 'foo'
        ])->json('token');

        $this->assertAuthenticatedAs($admin);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer' . $token,
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

        $this->assertNotNull($user);

        $token = $this->post('/api/login', [
            'email' => $user->email,
            'password' => 'foo'
        ])->json('token');

        $this->assertAuthenticatedAs($user);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer' . $token,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ])->json('PATCH', '/api/v1/user/atualizar-senha/' . $user->id, [
            'password' => fake()->password()
        ]);

        $response->assertStatus(200);
    }

    public function test_auth_user_patch_required_password()
    {
        $admin = User::factory()->create([
            'email_verified_at' => Date::now(),
            'type' => 'admin']
        );
        $user = User::factory()->create(['email_verified_at' => Date::now()]);

        $this->assertNotNull($admin);
        $this->assertNotNull($user);

        $token = $this->post('/api/login', [
            'email' => $admin->email,
            'password' => 'foo'
        ])->json('token');

        $this->assertAuthenticatedAs($admin);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer' . $token,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ])->json('PATCH', '/api/v1/user/atualizar-senha/' . $user->id);

        $response->assertInvalid(['password']);
        $response->assertJson(['message' => 'O campo password é o obrigatório.']);
        $response->assertStatus(422);

    }

    public function test_auth_admin_can_delete_user()
    {
        $admin = User::factory()->create([
            'email_verified_at' => Date::now(),
            'type' => 'admin']
        );
        $user = User::factory()->create(['email_verified_at' => Date::now()]);

        $this->assertNotNull($admin);
        $this->assertNotNull($user);

        $token = $this->post('/api/login', [
            'email' => $admin->email,
            'password' => 'foo'
        ])->json('token');

        $this->assertAuthenticatedAs($admin);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer' . $token,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ])->json('delete', '/api/v1/user/deletar/' . $user->id);

        $this->assertSoftDeleted($user);
        $response->assertStatus(200);
        $response->assertJson(['success' => 'Usuário deletado com sucesso.']);

    }

    public function test_auth_user_can_delete_himself()
    {
        $user = User::factory()->create(['email_verified_at' => Date::now()]);

        $this->assertNotNull($user);

        $token = $this->post('/api/login', [
            'email' => $user->email,
            'password' => 'foo'
        ])->json('token');

        $this->assertAuthenticatedAs($user);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer' . $token,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ])->json('delete', '/api/v1/user/deletar/' . $user->id);

        $this->assertSoftDeleted($user);
        $response->assertStatus(200);
        $response->assertJson(['success' => 'Usuário deletado com sucesso.']);

    }

    public function test_unauthorized_user_should_not_delete_another_user()
    {
        $user1 = User::factory()->create([
            'email_verified_at' => Date::now(),
            'type' => 'user'
        ]);
        $user2 = User::factory()->create([
            'email_verified_at' => Date::now(),
            'type' => 'user'
        ]);

        $this->assertNotNull($user1);
        $this->assertNotNull($user2);

        $token = $this->post('/api/login', [
            'email' => $user1->email,
            'password' => 'foo'
        ])->json('token');

        $this->assertAuthenticatedAs($user1);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer' . $token,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ])->json('delete', '/api/v1/user/deletar/' . $user2->id);

        $response->assertStatus(403);
        $response->assertJson(['error' => 'Usuário não autorizado.']);

    }

    public function test_auth_admin_can_create_admin()
    {
        $admin = User::factory()->create([
            'email_verified_at' => Date::now(),
            'type' => 'admin']
        );

        $this->assertNotNull($admin);

        $token = $this->post('/api/login', [
            'email' => $admin->email,
            'password' => 'foo'
        ])->json('token');

        $this->assertAuthenticatedAs($admin);

        $data = User::factory()->make();

        $this->assertNotNull($data);
        $this->assertIsArray($data->toArray());

        $response = $this->withHeaders([
            'Authorization' => 'Bearer' . $token,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ])->json('post', '/api/v1/user/cadastro/admin', $data->toArray());

        $response->assertStatus(201);

    }

    public function test_unauthorized_user_should_not_create_another_admin()
    {
        $user = User::factory()->create([
            'email_verified_at' => Date::now(),
            'type' => 'user'
        ]);

        $this->assertNotNull($user);

        $token = $this->post('/api/login', [
            'email' => $user->email,
            'password' => 'foo'
        ])->json('token');

        $this->assertAuthenticatedAs($user);

        $data = User::factory()->make();

        $this->assertNotNull($data);
        $this->assertIsArray($data->toArray());

        $response = $this->withHeaders([
            'Authorization' => 'Bearer' . $token,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ])->json('POST', '/api/v1/user/cadastro/admin', $data->toArray());

        $response->assertStatus(403);
        $response->assertJson(['error' => 'Usuário não autorizado.']);

    }

    public function test_auth_admin_can_change_user_status()
    {
        $admin = User::factory()->create([
            'email_verified_at' => Date::now(),
            'type' => 'admin']
        );
        $user = User::factory()->create(['email_verified_at' => Date::now()]);

        $this->assertNotNull($admin);
        $this->assertNotNull($user);

        $token = $this->post('/api/login', [
            'email' => $admin->email,
            'password' => 'foo'
        ])->json('token');

        $this->assertAuthenticatedAs($admin);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer' . $token,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ])->json('PATCH', '/api/v1/user/mudar-status/' . $user->id, [
            'status' => false
        ]);

        $response->assertStatus(200);

    }

    public function test_unauthorized_user_should_not_put_another_user_status()
    {
        $user1 = User::factory()->create([
            'email_verified_at' => Date::now(),
            'type' => 'user'
        ]);
        $user2 = User::factory()->create([
            'email_verified_at' => Date::now(),
            'type' => 'user'
        ]);

        $this->assertNotNull($user1);
        $this->assertNotNull($user2);

        $token = $this->post('/api/login', [
            'email' => $user1->email,
            'password' => 'foo'
        ])->json('token');

        $this->assertAuthenticatedAs($user1);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer' . $token,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ])->json('PATCH', '/api/v1/user/mudar-status/' . $user2->id, [
            "status" => false
        ]);

        $response->assertStatus(403);
        $response->assertJson(['error' => 'Usuário não autorizado.']);


    }

    public function test_to_change_status_status_required()
    {
        $user1 = User::factory()->create([
            'email_verified_at' => Date::now(),
            'type' => 'admin'
        ]);
        $user2 = User::factory()->create([
            'email_verified_at' => Date::now(),
            'type' => 'user'
        ]);

        $this->assertNotNull($user1);
        $this->assertNotNull($user2);

        $token = $this->post('/api/login', [
            'email' => $user1->email,
            'password' => 'foo'
        ])->json('token');

        $this->assertAuthenticatedAs($user1);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer' . $token,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ])->json('PATCH', '/api/v1/user/mudar-status/' . $user2->id);

        $response->assertJson(['error' => 'Status é Obrigatório.']);
        $response->assertStatus(422);

    }






}

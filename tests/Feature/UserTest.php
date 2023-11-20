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
        $user = User::factory()->make();

        $response = $this->json('POST', '/api/user/cadastro', $user->toArray());

        $this->assertDatabaseHas('users', [
            'firstName' => $user['firstName'],
            'lastName' => $user['lastName'],
            'email' => $user['email'],
            'cpf' => $user['cpf'],
            'phone' => $user['phone'],
        ]);

        $response->assertStatus(201);
    }

    public function test_user_store_firstName_required()
    {
        $user = User::factory()->make(['firstName' => null]);

        $response = $this->json('POST', '/api/user/cadastro', $user->toArray());

        $response->assertJson(['message' => 'O campo first name é o obrigatório.']);
        $response->assertStatus(422);
    }

    public function test_user_store_lastName_required()
    {
        $user = User::factory()->make(['lastName' => null]);

        $response = $this->json('POST', '/api/user/cadastro', $user->toArray());

        $response->assertJson(['message' => 'O campo last name é o obrigatório.']);
        $response->assertStatus(422);
    }

    public function test_user_store_email_required()
    {
        $user = User::factory()->make(['email' => null]);

        $response = $this->json('POST', '/api/user/cadastro', $user->toArray());

        $response->assertJson(['message' => 'O campo email é o obrigatório.']);
        $response->assertStatus(422);
    }

    public function test_user_store_email_unique()
    {
        $user1 = User::factory()->make(['email' => 'email@test.com']);
        $user2 = User::factory()->make(['email' => 'email@test.com']);

        $this->json('POST', '/api/user/cadastro', $user1->toArray());
        $response = $this->json('POST', '/api/user/cadastro', $user2->toArray());

        $response->assertJson(['message' => 'E-mail já cadastrado no sistema.']);
        $response->assertStatus(422);
    }

    public function test_user_store_email_safe_email()
    {
        $user = User::factory()->make(['email' => 'email.test.com']);

        $response = $this->json('POST', '/api/user/cadastro', $user->toArray());

        $response->assertJson(['message' => 'O campo e-mail deve ser um endereço válido.']);
        $response->assertStatus(422);
    }

    public function test_user_store_cpf_required()
    {
        $user = User::factory()->make(['cpf' => null]);

        $response = $this->json('POST', '/api/user/cadastro', $user->toArray());

        $response->assertJson(['message' => 'O campo cpf é o obrigatório.']);
        $response->assertStatus(422);
    }

    public function test_user_store_cpf_unique()
    {
        $user1 = User::factory()->make(['cpf' => '00011122233']);
        $user2 = User::factory()->make(['cpf' => '00011122233']);

        $this->json('POST', '/api/user/cadastro', $user1->toArray());
        $response = $this->json('POST', '/api/user/cadastro', $user2->toArray());

        $response->assertJson(['message' => 'CPF já cadastrado no sistema.']);
        $response->assertStatus(422);
    }

    public function test_user_store_cpf_have_to_be_11_digits()
    {
        $user = User::factory()->make(['cpf' => '123456789']);

        $response = $this->json('POST', '/api/user/cadastro', $user->toArray());

        $response->assertJson(['message' => 'CPF digitado incorretamente.']);
        $response->assertStatus(422);
    }

    public function test_user_store_phone_required()
    {
        $user = User::factory()->make(['phone' => null]);

        $response = $this->json('POST', '/api/user/cadastro', $user->toArray());

        $response->assertJson(['message' => 'O campo phone é o obrigatório.']);
        $response->assertStatus(422);
    }

    public function test_user_store_password_required()
    {
        $user = User::factory()->make(['password' => null]);

        $response = $this->json('POST', '/api/user/cadastro', $user->toArray());

        $response->assertJson(['message' => 'O campo password é o obrigatório.']);
        $response->assertStatus(422);
    }

    // AuthTest
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
            'type' => 'admin'
        ]);
        $user = User::factory()->create(['email_verified_at' => Date::now()]);

        $token = $this->post('/api/login', [
            'email' => $admin->email,
            'password' => 'foo'
        ])->json('token');

        $this->withHeaders([
            'Authorization' => 'Bearer' . $token,
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

        $token = $this->post('/api/login', [
            'email' => $user->email,
            'password' => 'foo'
        ])->json('token');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer' . $token,
        ])->post('api/v1/me');

        $response->assertStatus(200);
    }

    public function test_auth_user_can_logout()
    {
        $user = User::factory()->create();

        $token = $this->post('/api/login', [
            'email' => $user->email,
            'password' => 'foo'
        ])->json('token');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer' . $token,
        ])->post('api/v1/logout');

        $response->assertStatus(200);

    }

    public function test_auth_user_can_refresh_token()
    {
        $user = User::factory()->create();

        $token = $this->post('/api/login', [
            'email' => $user->email,
            'password' => 'foo'
        ])->json('token');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer' . $token,
        ])->post('api/v1/refresh');

        $response->assertStatus(201);
    }

    public function test_auth_user_can_get_all_users()
    {
        $user = User::factory()->create(['email_verified_at' => Date::now()]);

        $token = $this->post('/api/login', [
            'email' => $user->email,
            'password' => 'foo'
        ])->json('token');

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

        $token = $this->post('/api/login', [
            'email' => $admin->email,
            'password' => 'foo'
        ])->json('token');

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

        $token = $this->post('/api/login', [
            'email' => $user->email,
            'password' => 'foo'
        ])->json('token');

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

        $token = $this->post('/api/login', [
            'email' => $user1->email,
            'password' => 'foo'
        ])->json('token');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer' . $token,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ])->get('/api/v1/user/pegar-um/' . $user2->id);

        $response->assertStatus(403);

    }

    public function test_auth_admin_can_put_user()
    {
        $admin = User::factory()->create([
            'email_verified_at' => Date::now(),
            'type' => 'admin'
        ]);
        $user = User::factory()->create(['email_verified_at' => Date::now()]);

        $token = $this->post('/api/login', [
            'email' => $admin->email,
            'password' => 'foo'
        ])->json('token');

        $data = User::factory()->make();

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

        $token = $this->post('/api/login', [
            'email' => $user->email,
            'password' => 'foo'
        ])->json('token');

        $data = User::factory()->make();

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

        $token = $this->post('/api/login', [
            'email' => $user->email,
            'password' => 'foo'
        ])->json('token');

        $data = User::factory()->make(['firstName' => null]);
        $response = $this->withHeaders([
            'Authorization' => 'Bearer' . $token,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ])->json('PUT', '/api/v1/user/atualizar/' . $user->id, $data->toArray());

        $response->assertJson(['message' => 'O campo first name é o obrigatório.']);
        $response->assertStatus(422);

    }

    public function test_auth_user_put_lastName_required()
    {
        $user = User::factory()->create(['email_verified_at' => Date::now()]);

        $token = $this->post('/api/login', [
            'email' => $user->email,
            'password' => 'foo'
        ])->json('token');

        $data = User::factory()->make(['lastName' => null]);
        $response = $this->withHeaders([
            'Authorization' => 'Bearer' . $token,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ])->json('PUT', '/api/v1/user/atualizar/' . $user->id, $data->toArray());


        $response->assertJson(['message' => 'O campo last name é o obrigatório.']);
        $response->assertStatus(422);

    }

    public function test_auth_user_put_email_required()
    {
        $user = User::factory()->create(['email_verified_at' => Date::now()]);

        $token = $this->post('/api/login', [
            'email' => $user->email,
            'password' => 'foo'
        ])->json('token');

        $data = User::factory()->make(['email' => null]);
        $response = $this->withHeaders([
            'Authorization' => 'Bearer' . $token,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ])->json('PUT', '/api/v1/user/atualizar/' . $user->id, $data->toArray());


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

        $data = User::factory()->make(['email' => 'emaildetest@test.com']);
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

        $token = $this->post('/api/login', [
            'email' => $user->email,
            'password' => 'foo',
        ])->json('token');

        $data = User::factory()->make(['email' => 'email.test.com']);
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

        $token = $this->post('/api/login', [
            'email' => $user->email,
            'password' => 'foo'
        ])->json('token');

        $data = User::factory()->make(['cpf' => null]);
        $response = $this->withHeaders([
            'Authorization' => 'Bearer' . $token,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ])->json('PUT', '/api/v1/user/atualizar/' . $user->id, $data->toArray());

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

        $token = $this->post('/api/login', [
            'email' => $user2->email,
            'password' => 'foo'
        ])->json('token');

        $data = User::factory()->make(['cpf' => '12345678911']);
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

        $token = $this->post('/api/login', [
            'email' => $user->email,
            'password' => 'foo'
        ])->json('token');

        $data = User::factory()->make(['cpf' => '123456789']);
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

        $token = $this->post('/api/login', [
            'email' => $user->email,
            'password' => 'foo'
        ])->json('token');

        $data = User::factory()->make(['phone' => null]);
        $response = $this->withHeaders([
            'Authorization' => 'Bearer' . $token,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ])->json('PUT', '/api/v1/user/atualizar/' . $user->id, $data->toArray());

        $response->assertJson(['message' => 'O campo phone é o obrigatório.']);
        $response->assertStatus(422);

    }

    public function test_auth_user_put_type_required()
    {
        $user = User::factory()->create(['email_verified_at' => Date::now()]);

        $token = $this->post('/api/login', [
            'email' => $user->email,
            'password' => 'foo'
        ])->json('token');

        $data = User::factory()->make(['type' => null]);
        $response = $this->withHeaders([
            'Authorization' => 'Bearer' . $token,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ])->json('PUT', '/api/v1/user/atualizar/' . $user->id, $data->toArray());

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

        $token = $this->post('/api/login', [
            'email' => $user1->email,
            'password' => 'foo'
        ])->json('token');

        $data = User::factory()->make();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer' . $token,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ])->json('PUT', '/api/v1/user/atualizar/' . $user2->id, $data->toArray());

        $response->assertStatus(403);

    }

    public function test_auth_admin_can_patch_user_password()
    {
        $admin = User::factory()->create([
            'email_verified_at' => Date::now(),
            'type' => 'admin']
        );
        $user = User::factory()->create(['email_verified_at' => Date::now()]);

        $token = $this->post('/api/login', [
            'email' => $admin->email,
            'password' => 'foo'
        ])->json('token');

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

        $token = $this->post('/api/login', [
            'email' => $user->email,
            'password' => 'foo'
        ])->json('token');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer' . $token,
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

        $token = $this->post('/api/login', [
            'email' => $admin->email,
            'password' => 'foo'
        ])->json('token');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer' . $token,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ])->json('delete', '/api/v1/user/deletar/' . $user->id);

        $this->assertSoftDeleted($user);
        $response->assertStatus(200);

    }

    public function test_auth_user_can_delete_himself()
    {
        $user = User::factory()->create(['email_verified_at' => Date::now()]);

        $token = $this->post('/api/login', [
            'email' => $user->email,
            'password' => 'foo'
        ])->json('token');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer' . $token,
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

        $token = $this->post('/api/login', [
            'email' => $admin->email,
            'password' => 'foo'
        ])->json('token');

        $data = User::factory()->make();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer' . $token,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ])->json('post', '/api/v1/user/cadastro/admin', $data->toArray());

        $response->assertStatus(201);

    }

    public function test_auth_admin_can_change_user_status()
    {
        $admin = User::factory()->create([
            'email_verified_at' => Date::now(),
            'type' => 'admin']
        );
        $user = User::factory()->create(['email_verified_at' => Date::now()]);

        $token = $this->post('/api/login', [
            'email' => $admin->email,
            'password' => 'foo'
        ])->json('token');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer' . $token,
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

        $token = $this->post('/api/login', [
            'email' => $user->email,
            'password' => 'foo'
        ])->json('token');

        Notification::fake();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer' . $token,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ])->json('POST', '/api/v1/email-verificacao');

        $response->assertStatus(200);
        Notification::assertSentTo($user, VerifyEmail::class);

    }

    public function test_auth_user_verified_should_not_send_email_verification()
    {
        $user = User::factory()->create(['email_verified_at' => Date::now()]);

        $token = $this->post('/api/login', [
            'email' => $user->email,
            'password' => 'foo'
        ])->json('token');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer' . $token,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ])->json('POST', '/api/v1/email-verificacao');

        $response->assertStatus(200)
                 ->assertJson(['message' => 'E-mail já verificado']);

    }




}

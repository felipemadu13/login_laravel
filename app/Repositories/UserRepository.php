<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\Log;

class UserRepository extends Repository
{
    protected static $model = User::class;

    public function register(object $attributes) {

       $users = self::Model()->all();

       $user = $this->create([
        'firstName' => $attributes->firstName,
        'lastName'  => $attributes->lastName,
        'email'     => $attributes->email,
        'cpf'       => $attributes->cpf,
        'phone'     => $attributes->phone,
        'type'      => $users->isEmpty() ? 'admin' : 'user',
        'password'  => bcrypt($attributes->password)
     ]);

        if(!$user) {
            throw new \Exception('Erro inesperado', 500);
        }

       return $user;
    }

    public function registerAdmin(object $attributes) {

        $user = $this->create([
         'firstName' => $attributes->firstName,
         'lastName'  => $attributes->lastName,
         'email'     => $attributes->email,
         'cpf'       => $attributes->cpf,
         'phone'     => $attributes->phone,
         'type'      => 'admin',
         'password'  => bcrypt($attributes->password)
      ]);

         if(!$user) {
             throw new \Exception('Erro inesperado', 500);
         }

        return $user;
     }

    public function updatePut(int $id, object $attributes)
    {
        $this->log($id, $attributes, 'ATUALIZOU');
        $user = $this->update($id, [
            'firstName' => $attributes->firstName,
            'lastName'  => $attributes->lastName,
            'email'     => $attributes->email,
            'cpf'       => $attributes->cpf,
            'phone'     => $attributes->phone,
            'type'      => $attributes->type
        ]);
        return $user;
    }

    public function updatePatch(int $id, object $attributes)
    {
        $this->log($id, $attributes, 'ATUALIZOU');
        $user = $this->update($id, [
            'password' => bcrypt($attributes->password)
        ]);

        return $user;
    }

    public function userDelete(int $id, object $attributes)
    {
        $this->log($id, $attributes, 'APAGOU');
        $user = $this->findById($id);
        $this->delete($id);
    }

    public function log(int $id, object $attributes, string $message)
    {

        $auth = auth()->user();
        $user = $this->findById($id);
        Log::channel('user')->info("{$auth->type}: {$auth->firstName} cpf: {$auth->cpf} ip: {$attributes->ip()} {$message} informações de {$user->type}: {$user->firstName} {$user->lastName} cpf: {$user->cpf}", [
            'autenticado_metodo'     => $attributes->method(),
            'autenticado_tipo'       => $auth->type,
            'autenticado_nome'       => $auth->firstName,
            'autenticado_id'         => $auth->id,
            'autenticado_cpf'        => $auth->cpf,
            'autenticado_ip'         => $attributes->ip(),
            'usuario_alvo_tipo'      => $user->type,
            'usuario_alvo_nome'      => $user->firstName,
            'usuario_alvo_sobrenome' => $user->lastName,
            'usuario_alvo_cpf'       => $user->cpf,
            'usuario_alvo_id'        => $user->id
        ]);
    }
}


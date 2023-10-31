<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository extends Repository
{
    protected static $model = User::class;
 // no controller você tipou as variaveis deve tipar aqui também
    public function register($attributes) {

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

       return $user;
    }
// no controller você tipou as variaveis deve tipar aqui também
    public function updatePut($id)
    {
        // você não pode usar a função global request() receba uma varial com essas informações
        // exemplo updatePut($id, $attributes)
        // cada vez que você usa o request() você está fazendo uma injeção de dependência
        // Testabilidade fica dificil de testar
        $user = $this->update($id, [
            'firstName' => request()->firstName,
            'lastName' => request()->lastName,
            'email' => request()->email,
            'cpf' => request()->cpf,
            'phone' => request()->phone,
            'type' => request()->type
        ]);
        return $user;
    }
// no controller você tipou as variaveis deve tipar aqui também
    public function updatePatch($id)
    {
        // você não pode usar a função global request() receba uma varial com essas informações
        // exemplo updatePatch($id, $attributes)
        // cada vez que você usa o request() você está fazendo uma injeção de dependência
        // Testabilidade fica dificil de testar
        $user = $this->update($id, [
            'password' => bcrypt(request()->password)
        ]);
        return $user;
    }
}


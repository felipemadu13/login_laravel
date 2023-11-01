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
    public function updatePut(string $id, $attributes)
    {

        $user = $this->update($id, [
            'firstName' => $attributes->firstName,
            'lastName' => $attributes->lastName,
            'email' => $attributes->email,
            'cpf' => $attributes->cpf,
            'phone' => $attributes->phone,
            'type' => $attributes->type
        ]);
        return $user;
    }
// no controller você tipou as variaveis deve tipar aqui também
    public function updatePatch(string $id, $attributes)
    {

        $user = $this->update($id, [
            'password' => bcrypt($attributes->password)
        ]);

        return $user;
    }
}


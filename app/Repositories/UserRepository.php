<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository extends Repository
{
    protected static $model = User::class;

    public function register(object $attributes, bool $admin) {

       $users = self::Model()->all();
       $user = $this->create([
        'firstName' => $attributes->firstName,
        'lastName'  => $attributes->lastName,
        'email'     => $attributes->email,
        'cpf'       => $attributes->cpf,
        'phone'     => $attributes->phone,
        'type'      => $users->isEmpty() || $admin ? 'admin' : 'user',
        'password'  => bcrypt($attributes->password)
     ]);

       return $user;
    }

    public function updatePut(int $id, object $attributes)
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

    public function updatePatch(int $id, object $attributes)
    {

        $user = $this->update($id, [
            'password' => bcrypt($attributes->password)
        ]);

        return $user;
    }

}


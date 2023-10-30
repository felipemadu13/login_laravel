<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository extends Repository
{

    protected static $model = User::class;

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

    public function updatePut($id)
    {
        $user = $this->update($id, [
            'firstName' => request()->firstName,
            'lastName' => request()->lastName,
            'email' => request()->email,
            'cpf' => request()->cpf,
            'phone' => request()->phone,
            'status' => request()->status,
            'type' => request()->type
        ]);
        return $user;
    }

    public function updatePatch($id)
    {
        $user = $this->update($id, [
            'password' => bcrypt(request()->password)
        ]);
        return $user;
    }




}


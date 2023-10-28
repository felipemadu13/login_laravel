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

    public function updatePut($request, $id)
    {
        // verificar se $user existe

        // update longo
        $user = User::where('id', $id)->update([
            'firstName' => request()->firstName,
            'lastName' => request()->lastName,
            'email' => request()->email,
            'cpf' => request()->cpf,
            'phone' => request()->phone,
            'status' => request()->status,
            'type' => request()->type,
            'password' => request()->password
        ]);
        return $user;


        // update curto
        $user = User::where('id',$id)->update([$request->all()]);
        return $user;

    }

    public function updatePath()
    {
        // trocar senha
    }




}


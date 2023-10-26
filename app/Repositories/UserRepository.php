<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository extends Repository
{

    public function __construct(User $user)
    {
        $this->setModel(User::class);
    }

    public function register($data)
    {

        $user = User::create([
            'firstName' => request()->firstName,
            'lastName' => request()->lastName,
            'email' => request()->email,
            'cpf' => request()->cpf,
            'phone' => request()->phone,
            'password' => request()->password
        ]);
        return $user;
    }

    // 1 logica
    // se a tabela user estiver vazia, o primeiro usuario será um usuario admin.

    public function update($id)
    {
        // verificar se $user existe

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
        // if ($request->method() == "put") {
        //     $user->update([$request->all()]);
        // }

    }


}


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
        $users = $this->getAll();
        $user = User::create([
            'firstName' => request()->firstName,
            'lastName' => request()->lastName,
            'email' => request()->email,
            'cpf' => request()->cpf,
            'phone' => request()->phone,
            'password' => bcrypt(request()->password),
            'type' => count($users) == 0 ? 'admin' : 'user'
        ]);
        if (!$user) {
            throw new \Exception("Erro ao cadastrar");
        }
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


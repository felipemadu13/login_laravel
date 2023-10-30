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

        $model = self::Model()::find($id);

        // update longo
        $user = $model->update([
            'firstName' => request()->firstName,
            'lastName' => request()->lastName,
            'email' => request()->email,
            'cpf' => request()->cpf,
            'phone' => request()->phone,
            'status' => request()->status,
            'type' => request()->type,
            'password' => bcrypt(request()->password)
        ]);
        return $user;


        // update curto
        // $user = User::where('id',$id)->update([$request->all()]);
        // return $user;

    }

    public function updatePatch($id)
    {
        $model = self::Model()::find($id);
        $user = $model->update([
            'password' => bcrypt(request()->password)
        ]);
        return $user;
    }




}


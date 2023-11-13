<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\Log;

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

        $this->logUpdate($id, $attributes);
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
        $this->logUpdate($id, $attributes);
        $user = $this->update($id, [
            'password' => bcrypt($attributes->password)
        ]);

        return $user;
    }

    public function logUpdate(int $id, object $attributes)
    {
        $user = $this->findById($id);
        Log::info('{type}:{firstName} id:{id} cpf:{cpf} IP:{ip} atualizou as informações do {targetType}:{targetfirstName} de CPF:{targetCPF} de id:{targetId}', [
            'type' => auth()->user()->type,
            'firstName' => auth()->user()->firstName,
            'id' => auth()->user()->id,
            'cpf' => auth()->user()->cpf,
            'ip' => $attributes->ip(),
            'targetType' => $user->type,
            'targetfirstName' => $user->firstName,
            'targetLastName' => $user->lastName,
            'targetCPF' => $user->cpf,
            'targetId' => $user->id
        ]);

    }

}


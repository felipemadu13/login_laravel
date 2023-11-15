<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use PhpParser\Node\Expr\Cast\Object_;

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
        $this->log($id, $attributes, true);
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
        $this->log($id, $attributes, true);
        $user = $this->update($id, [
            'password' => bcrypt($attributes->password)
        ]);

        return $user;
    }

    public function userDelete(int $id, object $attributes)
    {
        $this->log($id, $attributes, false);
        $user = $this->findById($id);
        $this->delete($id);
    }

    public function log(int $id, object $attributes, $message)
    {

        $message ? $message = 'ATUALIZOU' : $message = 'APAGOU';

        $auth = auth()->user();
        $user = $this->findById($id);
        Log::channel('user')->info("{$auth->type}: {$auth->firstName} cpf: {$auth->cpf} ip: {$attributes->ip()} {$message} informações de {$user->type}: {$user->firstName} {$user->lastName} cpf: {$user->cpf}", [
            'autenticado tipo' => auth()->user()->type,
            'autenticado nome' => auth()->user()->firstName,
            'autenticado id' => auth()->user()->id,
            'autenticado cpf' => auth()->user()->cpf,
            'autenticado ip' => $attributes->ip(),
            'usuario alvo tipo' => $user->type,
            'usuario alvo nome' => $user->firstName,
            'usuario alvo sobrenome' => $user->lastName,
            'usuario alvo cpf' => $user->cpf,
            'usuario alvo id' => $user->id
        ]);
    }


}


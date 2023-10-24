<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Models\User;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();
        return response()->json($users, 200);
        // dd($users -> toArray());
    }

    public function store(Request $request)
    {
        User::create([
            'firstName' => request()->firstName,
            'lastName' => request()->lastName,
            'email' => request()->email,
            'cpf' => request()->cpf,
            'phone' => request()->phone,
            'status' => request()->status,
            'type' => request()->type,
            'password' => request()->password
        ]);

        return response()->json('Usuário cadastrado com sucesso.', 200);
    }

    public function show(string $id)
    {
        $user = User::find($id);
        return response()->json($user);
    }

    public function update(Request $request, string $id)
    {
        $user = User::find($id);
        $user->firstName = request()->firstName;
        $user->lastName = request()->lastName;
        $user->email = request()->email;
        $user->cpf = request()->cpf;
        $user->phone = request()->phone;
        $user->status = request()->status;
        $user->type = request()->type;
        $user->password = request()->password;

        $user->save();
        return response()->json($user);
    }

    public function destroy(string $id)
    {
        $user = User::find($id);
        $user->delete();
        return response()->json('Usuário deletado com sucesso');
    }
}

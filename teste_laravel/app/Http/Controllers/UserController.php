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
        $user = User::create([
            'firstName' => request()->firstName,
            'lastName' => request()->lastName,
            'email' => request()->email,
            'cpf' => request()->cpf,
            'phone' => request()->phone,
            'status' => false,
            'type' => 'admin',
            'password' => '123456dois'
        ]);

        return response()->json('Usuário cadastrado com sucesso.', 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $userId = User::find($id);
        return response()->json($userId);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}

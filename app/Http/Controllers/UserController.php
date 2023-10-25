<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Repositories\UserRepository;



class UserController extends Controller
{
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function index(Request $request)
    {
        try {
            $users =  $this->userRepository->getAll();
            return  response()->json($users, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {

       $user = User::create([
            'firstName' => request()->firstName,
            'lastName' => request()->lastName,
            'email' => request()->email,
            'cpf' => request()->cpf,
            'phone' => request()->phone,
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
        // $user->firstName = request()->firstName;
        // $user->lastName = request()->lastName;
        // $user->email = request()->email;
        // $user->cpf = request()->cpf;
        // $user->phone = request()->phone;
        // $user->status = request()->status;
        // $user->type = request()->type;
        // $user->password = request()->password;
        // $user->save();

        // verificar se $user existe

        if ($request->method() == "put") {
            dd($request->all());
            $user->update([$request->all()]);
        }


        return response()->json($user);
    }

    public function destroy(string $id)
    {
        $user = User::find($id);
        $user->delete();
        return response()->json('Usuário deletado com sucesso');
    }
}

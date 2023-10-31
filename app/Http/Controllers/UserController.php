<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use Illuminate\Http\Request;
// vc não está usando o metodo model User? se não tiver apague
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
            $users = $this->userRepository->findAll();
            return  response()->json($users, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], $e->getCode());
        }
    }

    public function store(UserRequest $request)
    {
        try {
            $user =  $this->userRepository->register($request);
            // preciso verificar o que tem dentro do $user
            // se $user  um usuario cadastrado retorne return response()->json(['sucess' => $user], 201);
            // se não retorne o erro
            return response()->json(['sucess' => $user], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], $e->getCode());
        }
    }

    public function show(string $id)
    {
        try {
            $users =  $this->userRepository->findById($id);
            return  response()->json($users, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], $e->getCode());
        }
    }

    public function update(Request $request, string $id)
    {
        try {
            // está muito repetitivo refatore seu código para reduzir os ifs
            // cade a validação ??
            if ($request->method() == "PUT") {
                // passe o request como uma variavel para a função updatePut
                $user = $this->userRepository->updatePut($id);
                // preciso que vc retorne código HTTP com sucesso
                return response()->json($user);
            }
            if ($request->method() == "PATCH") {
                // passe o request como uma variavel para a função updatePatch
                $user = $this->userRepository->updatePatch($id);
                // preciso que vc retorne código HTTP com sucesso
                return response()->json($user);
            }

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], $e->getCode());
        }

    }

    public function destroy(string $id)
    {
        try {
            $user =  $this->userRepository->delete($id);
            return response()->json(['sucess' => 'Usuário deletado com sucesso.'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], $e->getCode());
        }
    }

}

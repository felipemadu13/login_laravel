<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use Illuminate\Http\Request;
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
            if(!$user) {
                return response()->json(['error' => 'Erro ao cadastrar'], 404);
            }
            return response()->json(['success' => $user], 201);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], $e->getCode());
        }
    }

    public function show(int $id)
    {
        try {
            $users =  $this->userRepository->findById($id);
            return response()->json(['success' => $users], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], $e->getCode());
        }
    }

    public function update(UserRequest $request, int $id)
    {
        try {
            $user = $request->method() == "PUT"
            ? $this->userRepository->updatePut($id, $request)
            : $this->userRepository->updatePatch($id, $request);
            return response()->json(['success' => $user], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], $e->getCode());
        }
    }

    public function destroy(int $id)
    {
        try {
            $user =  $this->userRepository->delete($id);
            return response()->json(['success' => 'Usuário deletado com sucesso.'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], $e->getCode());
        }
    }

}

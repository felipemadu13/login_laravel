<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
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

            if ($request->method() == "PUT") {
                $user = $this->userRepository->updatePut($id);
                return response()->json($user);
            }
            if ($request->method() == "PATCH") {
                $user = $this->userRepository->updatePatch($id);
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

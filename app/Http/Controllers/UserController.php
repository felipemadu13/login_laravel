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
            $users = $this->userRepository->getAll();
            return response()->json($users, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        $user = $this->userRepository->register($request);
        return response()->json($user, 200);
    }

    public function show(string $id)
    {
        $user = $this->userRepository->getSingle($id);
        return response()->json($user);
    }

    public function update(Request $request, string $id)
    {
        $user = $this->userRepository->update($id);
        return response()->json($user);
    }

    public function destroy(string $id)
    {
        $user = $this->userRepository->delete($id);
        return response()->json('Usuário deletado com sucesso');
    }
}

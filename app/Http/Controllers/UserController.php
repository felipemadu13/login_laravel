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
        try {



            $user = $this->userRepository->register($request);
            return response()->json(["sucess"=>$user, 200]);


        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }




    }

    public function show(string $id)
    {
        $user = $this->userRepository->getSingle($id);
        return response()->json($user);
    }

    public function update(Request $request, string $id)
    {
        $user = $this->userRepository->updatePut($request, $id);
        return response()->json($user);

        // if ($request->method() == "put") {
        //     return 'Método PUT';
        // }

        // if ($request->method() == "patch") {
        //     return "Método PATCH";
        // }
    }

    public function destroy(string $id)
    {
        $user = $this->userRepository->delete($id);
        return response()->json('Usuário deletado com sucesso');
    }
}

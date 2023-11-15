<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use Illuminate\Http\Request;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

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

            $user =  $this->userRepository->register($request, false);
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
            if (!Gate::allows('verifyAuthorization', $id)) {
                return response()->json(['error' => 'Usuário não autorizado.'], 403);
            }

            $users =  $this->userRepository->findById($id);
            return response()->json(['success' => $users], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], $e->getCode());
        }
    }

    public function update(UserRequest $request, int $id)
    {
        try {
            if (!Gate::allows('verifyAuthorization', $id)) {
                return response()->json(['error' => 'Usuário não autorizado.'], 403);
            }
            $user = $request->method() == "PUT"
            ? $this->userRepository->updatePut($id, $request)
            : $this->userRepository->updatePatch($id, $request);

            return response()->json(['success' => $user], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], $e->getCode());
        }
    }

    public function destroy(Request $request, int $id)
    {
        try {
            if (!Gate::allows('verifyAuthorization', $id)) {
                return response()->json(['error' => 'Usuário não autorizado.'], 403);
            }

            $user = $this->userRepository->userDelete($id, $request);

            return response()->json(['success' => 'Usuário deletado com sucesso.'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], $e->getCode());
        }
    }

    public function storeAdmin(UserRequest $request)
    {
        try {
            if (!Gate::allows('isAdmin')) {
                return response()->json(['error' => 'Usuário não autorizado.'], 403);
            }
            $user =  $this->userRepository->register($request, true);
            if(!$user) {
                return response()->json(['error' => 'Erro ao cadastrar'], 404);
            }
            return response()->json(['success' => $user], 201);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], $e->getCode());
        }
    }

    public function changeUserStatus(Request $request, int $id)
    {
        try {
            if (!Gate::allows('isAdmin')) {
                return response()->json(['error' => 'Usuário não autorizado.'], 403);
            }

           $request->validate([
                'status' => ['required','boolean'],
           ]);

            $user = $this->userRepository->update($id, [
                'status' => $request->status
            ]);

            return response()->json(['success' => $user], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], $e->getCode());
        }

    // } catch (ValidationException $e) {
    //     return response()->json(['error' => 'status inválido'], 404);
    // } catch (\Exception $e) {
    //     return response()->json(['error' => $e->getMessage()], $e->getCode());
    // }
    }

}

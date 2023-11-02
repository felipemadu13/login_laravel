<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;
use App\Http\Requests\AuthRequest;
use App\Mail\PasswordReset;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;


class AuthController extends Controller
{
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }


    public function login(Request $request)
    {
        try {
            $credentials = $request->only(['email', 'password']);
            if (!$token = auth('api')->attempt($credentials)) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
            return $token;
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], $e->getCode());
        }
    }

    public function me()
    {
        try {
            return response()->json(auth('api')->user());
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], $e->getCode());
        }
    }

    public function logout()
    {
        try {
            auth('api')->logout();
            return response()->json(['success' => 'Sessão encerrada com sucesso'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], $e->getCode());
        }

    }

    public function refresh()
    {
        try {
            $newToken = auth('api')->refresh('api');
            return $newToken;
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], $e->getCode());
        }

    }

    public function passwordEmailReset(AuthRequest $request) {

        try {
            $email = $request->email;
            $findEmail = $this->userRepository->findEmail($email);

            if($findEmail) {
                Mail::to($email)->send(new PasswordReset());
                return response()->json($email, 202);
            }

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], $e->getCode());
        }
    }

    public function passwordReset(Request $request)
    {
        return response()->json("chegou na função passwordReset");
    }

}

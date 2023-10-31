<?php

namespace App\Http\Controllers;
// se você não tiver usando a classe Auth apague
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuthController extends Controller
{

    public function login(Request $request)
    {
        try {
            $credentials = $request->only(['email', 'password']);
            if (!$token = auth('api')->attempt($credentials)) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
            // não use o respondWithToken
            return $this->respondWithToken($token);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], $e->getCode());
        }
    }

    public function me()
    {
        return response()->json(auth('api')->user());
    }

    public function logout()
    {
        try {
            auth('api')->logout();
            return response()->json(['sucess' => 'Sessão encerrada com sucesso'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], $e->getCode());
        }

    }

    public function refresh()
    {
        // não use o respondWithToken
        return $this->respondWithToken(auth('api')->refresh('api'));
    }
    // não precisamos dessa função  respondWithToken aqui apague
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            // isso não deve exister em um controller questão de segurança
            // isso tem que ficar no .env
            // pesquise e traga a solução
            'expires_in' => auth('api')->factory()->getTTL() * 60
        ]);
    }
}

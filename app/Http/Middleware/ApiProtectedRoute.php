<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;

class ApiProtectedRoute

{

    public function handle(Request $request, Closure $next): Response
    {

        try {
            $user = JWTAuth::parseToken()->authenticate();
        } catch (\Exception $e) {
            if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException){
               return response()->json(['status'=>"Token Inválido"], 401);
            }else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException){
                return response()->json(['status'=>"Token Expirado"], 401);
            } else {
                return response()->json(['status' => "Token não encontrado"], 401);
            }
        }
        return $next($request);
    }
}

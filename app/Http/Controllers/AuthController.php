<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\AuthRequest;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        try {
            $credentials = $request->only(['email', 'password']);

            if (!$token = auth('api')->attempt($credentials)) {
                return response()->json(['error' => 'Não autorizado'], 401);
            }
            return response()->json(['token' => $token], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
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
            return response()->json(['error' => $e->getMessage()]);
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

    /**
    * A função passwordResetEmail é responsável por enviar o e-mail de recuperação de senha para o usuário.
    *
    * @param AuthRequest $request - O objeto de solicitação que contém os dados do usuário, incluindo o endereço de e-mail.
    *
    * @return array contendo um status de sucesso ou erro da operação de envio do e-mail de recuperação de senha.
    */
    public function passwordResetEmail(AuthRequest $request)
    {
        try {
            $status = Password::sendResetLink($request->only('email'));
            return $status === Password::RESET_LINK_SENT
                    ? ['status' => __($status)]
                    : ['error' => __($status)];
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], $e->getCode());
        }

    }

    /**

     * A função passwordResetUpdate é responsável por atualizar a senha do usuário após uma solicitação de redefinição de senha.
     *
    * @param AuthRequest $request - O objeto de solicitação que contém os dados necessários para a atualização da senha (email, nova senha, confirmação da senha e token de redefinição).
    *
    * @return \Illuminate\Http\Response - Uma resposta HTTP indicando o resultado da operação de atualização da senha.
    */
    public function passwordResetUpdate(AuthRequest $request)
    {
        try {
            $status = Password::reset(
                $request->only('email', 'password', 'password_confirmation', 'token'),
                function ($user) use ($request) {
                    $user->forceFill([
                    'password' => bcrypt($request->password),
                    'remember_token' => Str::random(60),
                ])->save();

                    $user->tokens()->delete();
                    // não gostei dessa forma de instancia uma dependência veja se consegue fazer sem o 'new'
                    event(new PasswordReset($user));
                }
            );

            if ($status == Password::PASSWORD_RESET) {
                return response()->json(['message' => 'Senha alterada com sucesso']);
            }
            return response()->json(['message' => __($status)], 500);

        } catch (\Exception $e) {
            return response()->json(['message' => 'Ocorreu um erro inesperado ao atualizar a senha.'], 500);
        }
    }




}

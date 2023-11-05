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
            // isso tá errado tem que retornar um json
            return $token;
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
    // preciso que detalhe o que essa função faz aqui no codigo  exemplo abaixo
     /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array<int, class-string|string>
     */
    public function passwordResetEmail(AuthRequest $request)
    {
        //cloque dentro de try catch
        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
                ? ['status' => __($status)]
                : ['error' => __($status)];

    }

    // preciso que detalhe o que essa função faz aqui no codigo  exemplo abaixo
     /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array<int, class-string|string>
     */

    public function passwordResetUpdate(AuthRequest $request)
    {
         //cloque dentro de try catch
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
            return response([
                'message'=> 'Senha alterada com sucesso'
            ]);
        }

        return response([
            'message'=> __($status)
        ], 500);

    }



}

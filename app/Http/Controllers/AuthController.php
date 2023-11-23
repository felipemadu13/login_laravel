<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\AuthRequest;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class AuthController extends Controller
{
        /**
     * @OA\Post(
     *     path="/api/login",
     *     summary="Autenticação do usuário",
     *     description="Autentica um usuário e obter um token de acesso.",
     *     operationId="login",
     *     tags={"AuthController"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "password"},
     *             @OA\Property(property="email", type="string", format="email", example="rhood@test.com"),
     *             @OA\Property(property="password", type="string", format="password", example="floresta459")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Autenticação bem-sucedida",
     *         @OA\JsonContent(
     *             @OA\Property(property="token", type="string", description="Token de acesso", example="eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6IkpvaG4gRG9lIiwiaWF0IjoxNTE2MjM5MDIyfQ.SflKxwRJSMeKKF2QT4fwpMeJf36POk6yJV_adQssw5c")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Credenciais inválidas",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", description="Senha ou email inválido", example="Senha ou email inválido")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Usuário inativo ou inexistente",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", description="Usuário inativo ou inexistente", example="Usuário inativo ou inexistente")
     *         )
     *     ),
     *      @OA\Response(
     *         response=500,
     *         description="Erro interno do servidor",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Mensagem de erro do sistema")
     *         )
     *     )
     *  ),
     * )
     */
    public function login(Request $request)
    {
        try {
            $user = User::where('email', $request->email)->first();

            if (!$user || !$user->status) {
                return response()->json(['error' => 'Usuário inativo ou inexistente'], 403);
            }

            $credentials = $request->only(['email', 'password']);
            if (!$token = auth('api')->attempt($credentials)) {
                return response()->json(['error' => 'Senha ou email inválido'], 401);
            }

            return response()->json(['token' => $token], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

        /**
     *
     *
     * @OA\Post(
     *      path="/api/v1/me",
     *      operationId="me",
     *      tags={"AuthController"},
     *      summary="Mostra detalhes do usuário autenticado",
     *      description="Retorna detalhes do usuário autenticado.",
     *      security={{"bearerAuth":{}}},
     *    @OA\Response(
     *          response=200,
     *          description="Operação bem-sucedida. Retorna os dados atualizados do usuário.",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="success",
     *                 type="object",
     *                 description="Exibe as informações do usuário autenticado",
     *                 @OA\Property(property="id", type="integer", description="ID do usuário", example=2),
     *                 @OA\Property(property="firstName", type="string", description="Primeiro nome do usuário", example="Robin"),
     *                 @OA\Property(property="lastName", type="string", description="Sobrenome do usuário", example="Hood"),
     *                 @OA\Property(property="email", type="string", format="email", description="E-mail do usuário", example="rhood@test.com"),
     *                 @OA\Property(property="cpf", type="string", description="CPF do usuário", example="00000000002"),
     *                 @OA\Property(property="phone", type="string", description="Número de telefone do usuário", example="81996501010"),
     *                 @OA\Property(property="status", type="boolean", example=false),
     *                 @OA\Property(property="type", type="string", description="Tipo de usuário", example="user"),
     *                 @OA\Property(property="email_verified_at", type="string", format="date-time", example=null),
     *                 @OA\Property(property="password", type="string", description="Senha do usuário", example="$2y$10$xYPET73j6cGcd2cQGA03DOd3R2AyVwoY3mddzvERatUle1a9KANrO"),
     *                 @OA\Property(property="created_at", type="string", description="Data de criação do usuário", example="2023-11-23T10:20:17.000000Z"),
     *                 @OA\Property(property="updated_at", type="string", description="Data de atualização do usuário", example="2023-11-23T10:20:17.000000Z"),
     *                 @OA\Property(property="deleted_at", type="string", format="date-time", example=null),
     *             )
     *         )
     *      ),
     *      @OA\Response(
     *         response=500,
     *         description="Erro interno do servidor",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Mensagem de erro do sistema")
     *         )
     *     ),
     * )
     * )
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function me()
    {
        try {
            return response()->json(auth('api')->user(), 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

        /**
     * @OA\Post(
     *     path="/api/v1/logout",
     *     summary="Encerra a sessão do usuário",
     *     description="Encerra a sessão do usuário autenticado",
     *     tags={"AuthController"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Sessão encerrada com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="string", example="Sessão encerrada com sucesso")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro interno do servidor",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Mensagem de erro do sistema")
     *         )
     *     )
     * )
     */
    public function logout()
    {
        try {
            auth('api')->logout();
            return response()->json(['success' => 'Sessão encerrada com sucesso'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

        /**
     * @OA\Post(
     *     path="/api/v1/refresh",
     *     summary="Atualiza o token de autenticação",
     *     description="Obtém um novo token de acesso com base no token atual.",
     *     operationId="refresh",
     *     tags={"AuthController"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=201,
     *         description="Token atualizado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="Novo Token", type="string", description="Novo token de acesso", example="eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6IkpvaG4gRG9lIiwiaWF0IjoxNTE2MjM5MDIyfQ.SflKxwRJSMeKKF2QT4fwpMeJf36POk6yJV_adQssw5c")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro interno do servidor",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", description="Mensagem de erro detalhada", example="Mensagem de erro do sistema")
     *         )
     *     ),
     * )
     */
    public function refresh()
    {
        try {
            $newToken = auth('api')->refresh('api');
            return response()->json(['Novo Token' => $newToken], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


        /**
     * @OA\Post(
     *     path="/api/forgot-password/email-recuperacao",
     *     summary="Enviar link de redefinição de senha por e-mail",
     *     tags={"AuthController"},
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             required={"email"},
     *             @OA\Property(property="email", type="string", example="user@test.com")
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Link de redefinição de senha enviado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="Enviamos o link de redefinição de senha para o seu e-mail.")
     *         )
     *     ),
     *     @OA\Response(
     *         response="422",
     *         description="Erro ao enviar o link de redefinição de senha",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Não encontramos um usuário com esse endereço de e-mail.")
     *         )
     *     ),
     *     @OA\Response(
     *         response="500",
     *         description="Erro interno do servidor",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Mensagem de erro do sistema")
     *         )
     *     )
     * )
     *
     * @param \App\Http\Requests\AuthRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function passwordResetEmail(AuthRequest $request)
    {
        try {
            $status = Password::sendResetLink($request->only('email'));
            return $status === Password::RESET_LINK_SENT
                ? ['status' => __($status)]
                : ['error' => __($status)];
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     *
     * @OA\Post(
     *      path="api/forgot-password/nova-senha",
     *      operationId="passwordResetUpdate",
     *      tags={"AuthController"},
     *      summary="Atualiza a senha do usuário após redefinição",
     *      description="Atualiza a senha do usuário após uma solicitação de redefinição de senha bem-sucedida.",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"email", "password", "password_confirmation", "token"},
     *              @OA\Property(property="email", type="string", format="email", example="usuario@test.com"),
     *              @OA\Property(property="password", type="string", format="password", example="nova_senha"),
     *              @OA\Property(property="password_confirmation", type="string", format="password", example="nova_senha"),
     *              @OA\Property(property="token", type="string", example="token_de_redefinicao"),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Redefinição de senha",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Senha alterada com sucesso"),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Erro ao processar a solicitação",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Mensagem de erro do sistema"),
     *          ),
     *      ),
     * )
     *
     * @param AuthRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function passwordResetUpdate(AuthRequest $request)
    {
        try {
            $status = Password::reset(
                $request->only('email', 'password', 'password_confirmation', 'token'),
                function ($user) use ($request) {
                    $user->forceFill([
                        'password'       => bcrypt($request->password),
                        'remember_token' => Str::random(60),
                    ])->save();

                    $user->tokens()->delete();

                    event(new PasswordReset($user));
                }
            );

            if ($status == Password::PASSWORD_RESET) {
                return response()->json(['message' => 'Senha alterada com sucesso'], 200);
            }
            return response()->json(['message' => __($status)], 500);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }



    /**
     * @OA\Post(
     *     path="/api/v1/email-verificacao",
     *     summary="Envia um e-mail de verificação para o usuário",
     *     description="Verifica se o e-mail do usuário já foi verificado. Se não, envia um e-mail de verificação.",
     *     operationId="verificationEmailSend",
     *     security={{"bearerAuth":{}}},
     *     tags={"AuthController"},
     *     @OA\Response(
     *         response=200,
     *         description="E-mail de verificação enviado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="E-mail de verificação enviado.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro interno do servidor",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Mensagem de erro do sistema")
     *         )
     *     )
     * )
     */
    public function verificationEmailSend(Request $request)
    {

        try {

            if ($request->user()->hasVerifiedEmail()) {
                return response()->json(['message' => 'E-mail já verificado'], 200);
            }

            $request->user()->sendEmailVerificationNotification();
            return response()->json(['message' => 'E-mail de verificação enviado.'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

        /**
     * @OA\Post(
     *      path="/api/v1/email/verify/{id}/{hash}",
     *      operationId="verifyEmail",
     *      tags={"AuthController"},
     *      summary="Verifica o e-mail do usuário",
     *      security={{"bearerAuth":{}}},
     *      description="Verifica se o e-mail do usuário foi verificado. Em caso afirmativo, retorna uma mensagem indicando que o e-mail já foi verificado. Caso contrário, marca o e-mail como verificado e retorna uma mensagem de sucesso.",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              @OA\Property(property="token", type="string", example="token_do_email"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="E-mail verificado com sucesso",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="E-mail verificado"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Erro interno do servidor",
     *          @OA\JsonContent(
     *              @OA\Property(property="error", type="string", example="Mensagem de erro do sistema")
     *          )
     *      ),
     * )
     */
    public function verificationEmailVerify(EmailVerificationRequest $request)
    {

        try {
            if ($request->user()->hasVerifiedEmail()) {
                return response()->json(['message' => 'E-mail já verificado'], 200);
            }

            if ($request->user()->markEmailAsVerified()) {
                event(new Verified($request->user()));
            }

            return response()->json(['message' => 'E-mail verificado'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}

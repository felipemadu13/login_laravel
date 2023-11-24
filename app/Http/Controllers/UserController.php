<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use Illuminate\Http\Request;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;


    /**
     * UserController
     *
     * @property-read \App\Repositories\UserRepository $userRepository
     */
class UserController extends Controller
{
		private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;

    }


    /**
     * @OA\Get(
     *     path="api/v1/user/pegar-todos",
     *     summary="Listar todos os usuários do sistema",
     *     description="Caso o usuário seja admin, retorna todos os usuários. Caso o usuário seja do tipo user, retorna o próprio usuário.",
     *     operationId="index",
     *     tags={"UserController"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de usuários",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=2),
     *                 @OA\Property(property="firstName", type="string", example="Robin"),
     *                 @OA\Property(property="lastName", type="string", example="Hood"),
     *                 @OA\Property(property="email", type="string", example="rhood@teste.com"),
     *                 @OA\Property(property="cpf", type="string", example="00000000002"),
     *                 @OA\Property(property="phone", type="string", example="81996501010"),
     *                 @OA\Property(property="status", type="boolean", example=true),
     *                 @OA\Property(property="type", type="string", example="user", enum={"user", "admin"}),
     *                 @OA\Property(property="email_verified_at", type="string", format="date-time", example=null),
     *                 @OA\Property(property="password", type="string", example="$2y$10$iOKcdpzCHckZM6Xs6yRGq.PQpD.TdxHy1VHly71cPE0Z1uaabtKiG"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2023-11-21T12:24:19.000000Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2023-11-21T12:24:19.000000Z"),
     *                 @OA\Property(property="deleted_at", type="string", format="date-time", example=null),
     *             )
     *         )
     *     ),
     *         @OA\Response(
     *         response=404,
     *         description="Nenhum dado encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Nenhum dado encontrado.")
     *         )
     *     ),
     *         @OA\Response(
     *         response=500,
     *         description="Erro interno do servidor",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Mensagem de erro do sistema")
     *         )
     *     ),
     * )
     */
    public function index(Request $request)
    {
        try {

            if (!Gate::allows('isAdmin')) {
                $user = $this->userRepository->findById(auth()->user()->id);
                return response()->json($user, 200);
            }

            $users = $this->userRepository->findAll();
            return  response()->json($users, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], $e->getcode() ? $e->getCode() : 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/user/cadastro",
     *     summary="Cria um novo usuário",
     *     description="Endpoint para cadastrar um novo usuário",
     *     operationId="store",
     *     tags={"UserController"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Dados do usuário a ser cadastrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="firstName", type="string", description="Primeiro nome do usuário", example="Robin"),
     *             @OA\Property(property="lastName", type="string", description="Sobrenome do usuário", example="Hood"),
     *             @OA\Property(property="email", type="string", format="email", description="E-mail do usuário", example="rhood@test.com"),
     *             @OA\Property(property="cpf", type="string", description="CPF do usuário com 11 digitos", example="00000000002"),
     *             @OA\Property(property="phone", type="string", description="Número de telefone do usuário", example="81996501010"),
     *             @OA\Property(property="password", type="string", format="password", description="Senha do usuário", example="floresta459")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Usuário cadastrado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="success",
     *                 type="object",
     *                 description="Detalhes do usuário cadastrado",
     *                 @OA\Property(property="firstName", type="string", description="Primeiro nome do usuário", example="Robin"),
     *                 @OA\Property(property="lastName", type="string", description="Sobrenome do usuário", example="Hood"),
     *                 @OA\Property(property="email", type="string", format="email", description="E-mail do usuário", example="rhood@test.com"),
     *                 @OA\Property(property="cpf", type="string", description="CPF do usuário", example="00000000002"),
     *                 @OA\Property(property="phone", type="string", description="Número de telefone do usuário", example="81996501010"),
     *                 @OA\Property(property="type", type="string", description="Tipo de usuário", example="user"),
     *                 @OA\Property(property="password", type="string", description="Senha do usuário", example="$2y$10$xYPET73j6cGcd2cQGA03DOd3R2AyVwoY3mddzvERatUle1a9KANrO"),
     *                 @OA\Property(property="updated_at", type="string", description="Data de atualização do usuário", example="2023-11-23T10:20:17.000000Z"),
     *                 @OA\Property(property="created_at", type="string", description="Data de criação do usuário", example="2023-11-23T10:20:17.000000Z"),
     *                 @OA\Property(property="id", type="integer", description="ID do usuário", example=2)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Erro ao cadastrar o usuário",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", description="Mensagem de erro", example="Erro ao cadastrar")
     *         )
     *     ),
     *      @OA\Response(
     *         response=500,
     *         description="Erro interno do servidor",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Erro inesperado")
     *         )
     *     ),
     * )
     */
    public function store(UserRequest $request)
    {
        try {

            $user =  $this->userRepository->register($request);
            return response()->json(['success' => $user], 201);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], $e->getcode() ? $e->getCode() : 500);
        }
    }

    /**
     * @OA\Get(
     *      path="api/v1/user/pegar-um/{id}",
     *      operationId="show",
     *      tags={"UserController"},
     *      summary="Encontra um usuário pelo seu id",
     *      description="Retorna detalhes do usuário",
     *      security={{"bearerAuth":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="User ID",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer",
     *              format="int64"
     *          )
     *      ),
     *     @OA\Response(
     *         response=200,
     *         description="Busca o usuário de id correspondente",
     *          @OA\JsonContent(
     *             @OA\Property(
     *                 property="success",
     *                 type="object",
     *                 description="Detalhes do usuário cadastrado",
     *                 @OA\Property(property="id", type="integer", description="ID do usuário", example=2),
     *                 @OA\Property(property="firstName", type="string", description="Primeiro nome do usuário", example="Robin"),
     *                 @OA\Property(property="lastName", type="string", description="Sobrenome do usuário", example="Hood"),
     *                 @OA\Property(property="email", type="string", format="email", description="E-mail do usuário", example="rhood@test.com"),
     *                 @OA\Property(property="cpf", type="string", description="CPF do usuário", example="00000000002"),
     *                 @OA\Property(property="phone", type="string", description="Número de telefone do usuário", example="81996501010"),
     *                 @OA\Property(property="status", type="boolean", example=true),
     *                 @OA\Property(property="type", type="string", description="Tipo de usuário", example="user"),
     *                 @OA\Property(property="email_verified_at", type="string", format="date-time", example=null),
     *                 @OA\Property(property="password", type="string", description="Senha do usuário", example="$2y$10$xYPET73j6cGcd2cQGA03DOd3R2AyVwoY3mddzvERatUle1a9KANrO"),
     *                 @OA\Property(property="created_at", type="string", description="Data de criação do usuário", example="2023-11-23T10:20:17.000000Z"),
     *                 @OA\Property(property="updated_at", type="string", description="Data de atualização do usuário", example="2023-11-23T10:20:17.000000Z"),
     *                 @OA\Property(property="deleted_at", type="string", format="date-time", example=null),
     *             )
     *         )
     *     ),
     *      @OA\Response(
     *          response=403,
     *          description="O admin tem acesso a qualquer usuário e o user somente tem acesso a ele mesmo.",
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="error",
     *                  type="string",
     *                  description="Error message",
     *                  example="Usuário não autorizado."
     *              )
     *          )
     *      ),
     *         @OA\Response(
     *         response=404,
     *         description="Item solicitado não existe",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", description="Mensagem de erro", example="Item solicitado não existe")
     *         )
     *     ),
     *         @OA\Response(
     *         response=500,
     *         description="Erro interno do servidor",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Mensagem de erro do sistema")
     *         )
     *     ),
     * )
     */
    public function show(int $id)
    {
        try {
            if (!Gate::allows('verifyAuthorization', $id)) {
                return response()->json(['error' => 'Usuário não autorizado.'], 403);
            }

            $users =  $this->userRepository->findById($id);
            return response()->json(['success' => $users], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], $e->getcode() ? $e->getCode() : 500);
        }
    }
        /**
     * @OA\Put(
     *      path="/api/v1/user/atualizar/{id}",
     *      operationId="update",
     *      tags={"UserController"},
     *      summary="Atualiza um usuário",
     *      description="Atualiza um usuário com base no ID",
     *      security={{"bearerAuth":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="ID do usuário a ser atualizado",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer",
     *              format="int64"
     *          )
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          description="Dados do usuário a serem atualizados",
     *         @OA\JsonContent(
     *             @OA\Property(property="firstName", type="string", description="Primeiro nome do usuário", example="Robb"),
     *             @OA\Property(property="lastName", type="string", description="Sobrenome do usuário", example="Hood"),
     *             @OA\Property(property="email", type="string", format="email", description="E-mail do usuário", example="rhood@test.com"),
     *             @OA\Property(property="cpf", type="string", description="CPF do usuário com 11 digitos", example="00000000002"),
     *             @OA\Property(property="phone", type="string", description="Número de telefone do usuário", example="81996501010"),
     *             @OA\Property(property="type", type="string", description="tipo do usuário", example="user")
     *         )
     *     ),
     *      @OA\Response(
     *          response=200,
     *          description="Operação bem-sucedida. Retorna os dados atualizados do usuário.",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="success",
     *                 type="object",
     *                 description="Detalhes do usuário atualizado",
     *                 @OA\Property(property="id", type="integer", description="ID do usuário", example=2),
     *                 @OA\Property(property="firstName", type="string", description="Primeiro nome do usuário", example="Robin"),
     *                 @OA\Property(property="lastName", type="string", description="Sobrenome do usuário", example="Hood"),
     *                 @OA\Property(property="email", type="string", format="email", description="E-mail do usuário", example="rhood@test.com"),
     *                 @OA\Property(property="cpf", type="string", description="CPF do usuário", example="00000000002"),
     *                 @OA\Property(property="phone", type="string", description="Número de telefone do usuário", example="81996501010"),
     *                 @OA\Property(property="status", type="boolean", example=true),
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
     *          response=403,
     *          description="O admin tem acesso a qualquer usuário e o user somente tem acesso a ele mesmo.",
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="error",
     *                  type="string",
     *                  description="Mensagem de erro",
     *                  example="Usuário não autorizado."
     *              )
     *          )
     *      ),
     *         @OA\Response(
     *         response=500,
     *         description="Erro interno do servidor",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Erro inesperado")
     *         )
     *     ),
     * )
     *
     * @param UserRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */

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
            return response()->json(['error' => $e->getMessage()], $e->getcode() ? $e->getCode() : 500);
        }
    }
            /**
     * @OA\Delete(
     *      path="/api/v1/user/deletar/{id}",
     *      operationId="delete",
     *      tags={"UserController"},
     *      summary="Apaga um usuário",
     *      description="Apaga um usuário com base no ID",
     *      security={{"bearerAuth":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="ID do usuário a ser apagado",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer",
     *              format="int64"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Usuário apagado",
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="success",
     *                  type="string",
     *                  description="Mensagem de erro",
     *                  example="Usuário deletado com sucesso."
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="O admin tem acesso a qualquer usuário e o user somente tem acesso a ele mesmo.",
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="error",
     *                  type="string",
     *                  description="Mensagem de erro",
     *                  example="Usuário não autorizado."
     *              )
     *          )
     *      ),
     *         @OA\Response(
     *         response=404,
     *         description="Item solicitado não existe",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Item solicitado não existe")
     *         )
     *     ),
     *         @OA\Response(
     *         response=500,
     *         description="Erro interno do servidor",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Erro inesperado")
     *         )
     *     ),
     * )
     *
     * @param UserRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */

    public function destroy(Request $request, int $id)
    {
        try {
            if (!Gate::allows('verifyAuthorization', $id)) {
                return response()->json(['error' => 'Usuário não autorizado.'], 403);
            }

            $user = $this->userRepository->userDelete($id, $request);

            return response()->json(['success' => 'Usuário deletado com sucesso.'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], $e->getcode() ? $e->getCode() : 500);
        }
    }
        /**
     * @OA\Post(
     *      path="/api/v1/user/cadastro/admin",
     *      operationId="storeAdmin",
     *      tags={"UserController"},
     *      summary="Cadastrar novo administrador",
     *      description="Cria um novo administrador no sistema",
     *      security={{"bearerAuth":{}}},
     *      @OA\RequestBody(
     *         required=true,
     *         description="Dados do usuário a ser cadastrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="firstName", type="string", description="Primeiro nome do usuário", example="Robin"),
     *             @OA\Property(property="lastName", type="string", description="Sobrenome do usuário", example="Hood"),
     *             @OA\Property(property="email", type="string", format="email", description="E-mail do usuário", example="rhood@test.com"),
     *             @OA\Property(property="cpf", type="string", description="CPF do usuário com 11 digitos", example="00000000002"),
     *             @OA\Property(property="phone", type="string", description="Número de telefone do usuário", example="81996501010"),
     *             @OA\Property(property="password", type="string", format="password", description="Senha do usuário", example="floresta459")
     *         )
     *      ),
     *         @OA\Response(
     *         response=201,
     *         description="Administrador cadastrado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="success",
     *                 type="object",
     *                 description="Detalhes do administrador cadastrado",
     *                 @OA\Property(property="firstName", type="string", description="Primeiro nome do usuário", example="Robin"),
     *                 @OA\Property(property="lastName", type="string", description="Sobrenome do usuário", example="Hood"),
     *                 @OA\Property(property="email", type="string", format="email", description="E-mail do usuário", example="rhood@test.com"),
     *                 @OA\Property(property="cpf", type="string", description="CPF do usuário", example="00000000002"),
     *                 @OA\Property(property="phone", type="string", description="Número de telefone do usuário", example="81996501010"),
     *                 @OA\Property(property="type", type="string", description="Tipo de usuário", example="admin"),
     *                 @OA\Property(property="password", type="string", description="Senha do usuário", example="$2y$10$xYPET73j6cGcd2cQGA03DOd3R2AyVwoY3mddzvERatUle1a9KANrO"),
     *                 @OA\Property(property="updated_at", type="string", description="Data de atualização do usuário", example="2023-11-23T10:20:17.000000Z"),
     *                 @OA\Property(property="created_at", type="string", description="Data de criação do usuário", example="2023-11-23T10:20:17.000000Z"),
     *                 @OA\Property(property="id", type="integer", description="ID do usuário", example=2)
     *             )
     *         )
     *     ),
     *      @OA\Response(
     *          response=403,
     *          description="O admin tem acesso a qualquer usuário e o user somente tem acesso a ele mesmo.",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="error", type="string", description="Mensagem de erro", example="Usuário não autorizado.")
     *          )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Erro ao cadastrar o administrador",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="error", type="string", description="Mensagem de erro", example="Erro ao cadastrar")
     *          )
     *      ),
     *         @OA\Response(
     *         response=500,
     *         description="Erro interno do servidor",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Mensagem de erro do sistema")
     *         )
     *     ),
     * )
     */

    public function storeAdmin(UserRequest $request)
    {
        try {
            if (!Gate::allows('isAdmin')) {
                return response()->json(['error' => 'Usuário não autorizado.'], 403);
            }
            $user =  $this->userRepository->registerAdmin($request);
            if(!$user) {
                return response()->json(['error' => 'Erro ao cadastrar'], 404);
            }
            return response()->json(['success' => $user], 201);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], $e->getcode() ? $e->getCode() : 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/v1/user/mudar-status/{id}",
     *     summary="Alterar o status de um usuário",
     *     description="Altera o status de um usuário. Apenas usuários com papel de administrador têm permissão.",
     *     operationId="changeUserStatus",
     *     security={{"bearerAuth":{}}},
     *     tags={"UserController"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID do usuário",
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Dados para a alteração do status do usuário",
     *         @OA\JsonContent(
     *             required={"status"},
     *             @OA\Property(property="status", type="boolean", description="Novo status do usuário (true para ativo, false para inativo)", example=false)
     *         )
     *     ),
     *       @OA\Response(
     *          response=200,
     *          description="Operação bem-sucedida. Retorna os dados atualizados do usuário.",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="success",
     *                 type="object",
     *                 description="Detalhes do usuário atualizado",
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
     *     @OA\Response(
     *         response=403,
     *         description="Acesso não autorizado",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", description="Mensagem de erro indicando acesso não autorizado", example="Usuário não autorizado.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erro de validação",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", description="Mensagem de erro indicando que o status é obrigatório", example="Status é Obrigatório.")
     *         )
     *     ),
     *         @OA\Response(
     *         response=500,
     *         description="Erro interno do servidor",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Mensagem de erro do sistema")
     *         )
     *     ),
     * )
     */
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
        } catch (ValidationException $e) {
            return response()->json(['error' => 'Status é Obrigatório.'], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], $e->getcode() ? $e->getCode() : 500);
        }
    }

}

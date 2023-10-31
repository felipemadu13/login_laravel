<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Middleware\ApiProtectedRoute;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// se não tiver usando essa rota apague
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('user')->group(function () {
    Route::get('/', [UserController::class, "index"]);
    Route::get('/{id}', [UserController::class, "show"]);
    Route::post('/', [UserController::class, "store"]);
    Route::put('/{id}', [UserController::class, "update"]);
    Route::patch('/{id}', [UserController::class, "update"]);
    Route::delete('/{id}', [UserController::class, "destroy"]);
});
// tá errado a forma que vc ta usando o middleware não precisa instaciar uma class tem na documetação
// o certo é middleware('jwt.auth')
// é uma boa pratica usar prefix para rotas protegidas por api exemplo v1, v2....
Route::middleware(ApiProtectedRoute::class)->group(function () {
    Route::post('me', [AuthController::class, "me"]);
});

    Route::post('login', [AuthController::class, "login"]);
    //logout é uma rota protegid! como você vai sair se vc nem entrou?
    // dessa forma fica vuneravel para obter dados de clientes
    Route::post('logout', [AuthController::class, "logout"]);
    //não tenho certeza mas acho que refresh deve ser uma rota progida pesquise e me diga!
    Route::post('refresh', [AuthController::class, "refresh"]);

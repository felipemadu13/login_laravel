<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
// todas as rota user devem fica protegidas por middleaware jwt. menos de cadastrar
// melhore a nomeclaturas das rotas user estão muito confusas.
Route::prefix('user')->group(function () {
    Route::get('/', [UserController::class, "index"]);
    Route::get('/{id}', [UserController::class, "show"]);
    Route::post('/', [UserController::class, "store"]);
    Route::put('/{id}', [UserController::class, "update"]);
    Route::patch('/{id}', [UserController::class, "update"]);
    Route::delete('/{id}', [UserController::class, "destroy"]);
});

Route::prefix('v1')->middleware('jwt.auth')->group(function () {
    Route::post('me', [AuthController::class, "me"]);
    Route::post('logout', [AuthController::class, "logout"]);
    Route::post('refresh', [AuthController::class, "refresh"]);
});
    // não está alinhado
    Route::post('login', [AuthController::class, "login"]);
// melhore a nomeclaturas das rotas forgot-password estão confusas;
Route::prefix('forgot-password')->middleware('guest')->group(function () {
    Route::post('/', [AuthController::class, "passwordResetEmail"]);
    // usando verbo post mas o nome da rota é update? kkkkkkkkkkkk
    Route::post('/update', [AuthController::class, "passwordResetUpdate"]);

// pague o espaço em branco
});

<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

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
    Route::post('login', [AuthController::class, "login"]);

// Rota de Teste
Route::prefix('teste')->group(function () {
    Route::post('/reset', [AuthController::class, "passwordReset"]);
});

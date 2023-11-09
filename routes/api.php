<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::post('user/cadastro', [UserController::class, "store"]);
Route::post('login', [AuthController::class, "login"]);

Route::prefix('forgot-password')->middleware('guest')->group(function () {
    Route::post('/email-recuperacao', [AuthController::class, "passwordResetEmail"]);
    Route::put('/nova-senha', [AuthController::class, "passwordResetUpdate"]);
});

Route::prefix('v1')->middleware(['jwt.auth'])->group(function () {
    Route::post('me', [AuthController::class, "me"]);
    Route::post('logout', [AuthController::class, "logout"]);
    Route::post('refresh', [AuthController::class, "refresh"]);

    Route::prefix('user')->middleware('verified')->group(function () {
        Route::get('/pegar-todos', [UserController::class, "index"]);
        Route::get('/pegar-um/{id}', [UserController::class, "show"]);
        Route::put('/atualizar/{id}', [UserController::class, "update"]);
        Route::patch('/atualizar-senha/{id}', [UserController::class, "update"]);
        Route::delete('/deletar/{id}', [UserController::class, "destroy"]);
        Route::post('cadastro/admin', [UserController::class, "storeAdmin"]);
    });

    Route::post('/email-verificacao', [AuthController::class, "verificationEmailSend"]);
    Route::get('/email/verify/{id}/{hash}', [AuthController::class, "verificationEmailVerify"])->name('verification.verify');
});



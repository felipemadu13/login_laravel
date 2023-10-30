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

Route::middleware(ApiProtectedRoute::class)->group(function () {
    Route::post('me', [AuthController::class, "me"]);
});

    Route::post('login', [AuthController::class, "login"]);
    Route::post('logout', [AuthController::class, "logout"]);
    Route::post('refresh', [AuthController::class, "refresh"]);

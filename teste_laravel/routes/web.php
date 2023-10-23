<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::prefix('user')->group(function () {
    Route::get('/', [UserController::class, "index"]);
});

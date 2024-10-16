<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CardsController;
use App\Http\Controllers\UserController;

// Mostrar un mensaje de bienvenida y que diga API version 1.0 en el inicio
Route::get('/', function () {
    return response()->json(['message' => 'API version 1.0']);
});

Route::group(['middleware' => 'api'], function ($routes) {
    Route::post('register-user', [UserController::class,'userRegister']);
    Route::post('login-user', [UserController::class,'userLogin']);
    Route::get('user-profile', [UserController::class,'userProfile']);
    Route::post('login-google', [UserController::class,'loginGoogle']);

    Route::group(['middleware' => 'auth:api'], function ($routes) {
        Route::prefix('cards')->group(function () {
            Route::get('/', [CardsController::class, 'index']);
            Route::post('/', [CardsController::class, 'store']);
            Route::get('/{id}', [CardsController::class, 'show']);
            Route::put('/{id}', [CardsController::class, 'update']);
        });
    });
});

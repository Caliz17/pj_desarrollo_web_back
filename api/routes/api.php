<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CardsController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DecksController;

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

        Route::prefix('decks')->group(function () {
            Route::get('/{deckId}/players/{playerId}', [DecksController::class, 'getDeckByPlayer']);
            Route::post('/players/{playerId}', [DecksController::class, 'createDeck']);
            Route::put('/{deckId}/players/{playerId}', [DecksController::class, 'updateDeck']);

        });

        Route::prefix('matches')->group(function () {
            Route::get('/players/{playerId}', [DecksController::class, 'getMatchesByPlayer']);
            Route::post('/players/{playerId}', [DecksController::class, 'createMatch']);
            Route::put('/{matchId}/players/{playerId}', [DecksController::class, 'updateMatch']);
        });
    });
});

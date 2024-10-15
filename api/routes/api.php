<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CardsController;

// Mostrar un mensaje de bienvenida y que diga API version 1.0 en el inicio
Route::get('/', function () {
    return response()->json(['message' => 'API version 1.0']);
});

// Grupo de rutas para las cards
Route::prefix('cards')->group(function () {
    Route::get('/', [CardsController::class, 'index']);
    Route::post('/', [CardsController::class, 'store']);
    Route::get('/{id}', [CardsController::class, 'show']);
    Route::put('/{id}', [CardsController::class, 'update']);
});

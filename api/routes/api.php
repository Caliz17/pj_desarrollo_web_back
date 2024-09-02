<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// mostarar un mensaje de bienvenida y que diga api version 1.0 en el inicio
Route::get('/', function () {
    return response()->json(['message' => 'API version 1.0']);
});

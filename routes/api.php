<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;


Route::get('/test', function () {
    return response()->json(['message' => 'API funcionando']);
});
Route::get('/ping', fn() => ['pong' => true]);


// Rutas públicas
// Route::post('/login', [AuthController::class, 'login']);
Route::post('/login', [AuthController::class, 'login'])->name('api.login');

// Rutas protegidas con token JWT
Route::middleware('auth:api')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
});


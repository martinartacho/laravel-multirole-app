<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\GestorController;
use Illuminate\Support\Facades\Route;

// Rutas públicas
Route::get('/', function () {
    return view('welcome');
});

// Rutas de autenticación
require __DIR__.'/auth.php';

// Rutas protegidas
Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard único
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Perfil de usuario
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Rutas de administrador
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::resource('users', \App\Http\Controllers\Admin\UserController::class);
    });
    
    // Rutas de gestor
    Route::middleware(['auth', 'role:gestor'])->prefix('gestor')->name('gestor.')->group(function () {
        Route::get('/users', [GestorUserController::class, 'index'])->name('users.index');
    });

});
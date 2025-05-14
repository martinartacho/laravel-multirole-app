<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Gestor\GestorController;
use App\Http\Controllers\Gestor\UserController as GestorUserController;
use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Route;

// Rutas lang
/* Route::post('/locale', function (\Illuminate\Http\Request $request) {
    Session::put('locale', $request->input('locale'));
    return back();
})->name('locale.set'); */

// Rutas públicas
Route::get('/', function () {
    return view('welcome');
});

// Rutas de autenticación
require __DIR__.'/auth.php';

// Rutas protegidas
Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard principal
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Perfil de usuario
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Rutas de administrador
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::resource('users', AdminUserController::class);
    });


    // Rutas para gestores (versión limpia)
     Route::middleware(['role:gestor'])->prefix('gestor')->name('gestor.')->group(function () {
        // Panel del gestor
        Route::get('/dashboard', [GestorController::class, 'dashboard'])->name('dashboard');
        
        // Gestión de usuarios
        Route::resource('users', GestorUserController::class)->only([
            'index', 'edit', 'update'
        ]);
        
    }); 

Route::middleware(['auth'])->group(function () {
    Route::get('/notifications', [NotificationController::class, 'index'])
        ->name('notifications.index')
        ->middleware('permission:notifications.view');

    Route::get('/notifications/create', [NotificationController::class, 'create'])
        ->name('notifications.create')
        ->middleware('permission:notifications.create');

    Route::post('/notifications', [NotificationController::class, 'store'])
        ->name('notifications.store')
        ->middleware('permission:notifications.create');

    Route::get('/notifications/{notification}', [NotificationController::class, 'show'])
        ->name('notifications.show')
        ->middleware('permission:notifications.view');

    Route::get('/notifications/{notification}/edit', [NotificationController::class, 'edit'])
        ->name('notifications.edit')
        ->middleware('permission:notifications.edit');

    Route::put('/notifications/{notification}', [NotificationController::class, 'update'])
        ->name('notifications.update')
        ->middleware('permission:notifications.edit');

    Route::delete('/notifications/{notification}', [NotificationController::class, 'destroy'])
        ->name('notifications.destroy')
        ->middleware('permission:notifications.delete');
});

    // CRUD principal de notificaciones
/*  Limiar esto 
    Route::resource('notifications', NotificationController::class);

    Route::post('notifications/{notification}/publish', [NotificationController::class, 'publish'])
        ->name('notifications.publish');
*/
    // API para notificaciones
    Route::prefix('api')->group(function () {
        Route::get('/notifications/unread-count', [NotificationController::class, 'getUnreadCount'])->name('notifications.unread-count');
        Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    });


/*     Route::get('/test-permission', function () {
        return 'Tienes permiso para ver esta ruta.';
    })->middleware('permission:notifications.view'); */



});
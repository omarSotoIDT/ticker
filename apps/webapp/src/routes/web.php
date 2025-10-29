<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PerfilController;

Route::get('/login', function () { return view('auth.login'); });
Route::post('/login', [AuthController::class, 'iniciarSesion']);

// Rutas para la gestión de Perfiles y Permisos
Route::prefix('perfiles')->middleware('auth')->group(function () {
    Route::get('/', [PerfilController::class, 'index'])->name('perfiles.index');
    Route::post('/', [PerfilController::class, 'guardar'])->name('perfiles.guardar');
    Route::put('/{perfil_id}', [PerfilController::class, 'actualizar'])->name('perfiles.actualizar');
    Route::patch('/eliminar/{perfil_id}', [PerfilController::class, 'eliminar'])->name('perfiles.eliminar');
});
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PerfilController;

Route::get('/login', function () { return view('auth.login'); });
Route::post('/login', [AuthController::class, 'iniciarSesion']);

// Rutas para la gestión de perfiles
Route::get('/perfiles', [PerfilController::class, 'index'])->name('perfiles.index'); 
Route::post('/perfiles', [PerfilController::class, 'guardar'])->name('perfiles.guardar');
Route::put('/perfiles/{perfil_id}', [PerfilController::class, 'actualizar'])->name('perfiles.actualizar');
Route::delete('/perfiles/{perfil_id}', [PerfilController::class, 'eliminar'])->name('perfiles.eliminar');
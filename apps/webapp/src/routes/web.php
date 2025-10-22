<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PerfilesController;

Route::get('/login', function () { return view('auth.login'); });
Route::post('/login', [AuthController::class, 'iniciarSesion']);

// Rutas para la gestión de perfiles
Route::get('/perfiles', [PerfilesController::class, 'index'])->name('perfiles.index'); 
Route::post('/perfiles', [PerfilesController::class, 'guardar'])->name('perfiles.guardar');
Route::put('/perfiles/{perfil_id}', [PerfilesController::class, 'actualizar'])->name('perfiles.actualizar');
Route::delete('/perfiles/{perfil_id}', [PerfilesController::class, 'eliminar'])->name('perfiles.eliminar');
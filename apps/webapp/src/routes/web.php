<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PerfilController;

Route::get('/login', function () { return view('auth.login'); });
Route::post('/login', [AuthController::class, 'iniciarSesion']);

// Rutas para la gestión de perfiles
Route::get('/perfiles', [PerfilController::class, 'index'])->name('perfiles.index');
Route::post('/perfiles', [PerfilController::class, 'store'])->name('perfiles.store');
Route::get('/perfiles/{id}/edit', [PerfilController::class, 'edit'])->name('perfiles.edit');
Route::put('/perfiles/{id}', [PerfilController::class, 'update'])->name('perfiles.update');
Route::delete('/perfiles/{id}', [PerfilController::class, 'destroy'])->name('perfiles.destroy');

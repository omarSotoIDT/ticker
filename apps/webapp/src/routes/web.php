<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/login', function () { return view('auth.login'); })->name('login');
Route::post('/login', [AuthController::class, 'iniciarSesion']);
Route::post('/logout', [AuthController::class, 'cerrarSesion'])->name('logout');
Route::get('/dashboard', function () { return view('dashboard'); })->middleware('auth')->name('dashboard');

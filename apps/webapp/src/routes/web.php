<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/login', function () { return view('auth.login'); });
Route::post('/login', [AuthController::class, 'iniciarSesion']);

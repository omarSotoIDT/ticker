<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\TicketFeedbackController;

Route::get('/login', function () { return view('auth.login'); })->name('login');
Route::post('/login', [AuthController::class, 'iniciarSesion']);
Route::post('/logout', [AuthController::class, 'cerrarSesion'])->name('logout');
Route::get('/dashboard', function () { return view('dashboard'); })->middleware('auth')->name('dashboard');

Route::prefix('usuarios')->group(function(){
    Route::middleware('auth')->group(function() {
        Route::get('/', [UsuarioController::class, 'gestor'])->name('usuarios.gestor');
        Route::get('/listarRest', [UsuarioController::class, 'listarRest'])->name('usuarios.listarRest');
        Route::post('/agregarRest', [UsuarioController::class, 'agregarRest'])->name('usuarios.agregarRest');
        Route::patch('/editarRest/{id}', [UsuarioController::class, 'editarRest'])->name('usuarios.editarRest');
        Route::patch('/eliminarRest/{id}', [UsuarioController::class, 'eliminarRest'])->name('usuarios.eliminarRest');
        Route::patch('/activarRest/{id}', [UsuarioController::class, 'activarRest'])->name('usuarios.activarRest');
    });
});

Route::prefix('tickets')->group(function () {
    Route::middleware('auth')->group(function() {
        Route::get('/', action: [TicketController::class, 'gestor'])->name('tickets.gestor');
        Route::get('/listadoRest', [TicketController::class, 'listarRest'])->name('tickets.listarRest');
        Route::get('{id}/detalleRest', [TicketController::class, 'obtenerRest'])->name('tickets.obtenerRest');
        Route::post('/registroRest', [TicketController::class, 'agregarRest'])->name('tickets.agregarRest');
        Route::patch('/{id}/edicionRest', [TicketController::class, 'editarRest'])->name('tickets.editarRest');
        Route::patch('/{id}/statusRest', [TicketController::class, 'editarStatusRest'])->name('tickets.editarStatusRest');
        Route::patch('/{id}/PrioridadRest', [TicketController::class, 'editarPrioridadRest'])->name('tickets.editarPrioridadRest');
        Route::patch('/{id}/AsignacionRest', [TicketController::class, 'editarAsignacionRest'])->name('tickets.editarAsignacionRest');
        Route::get('/{ticket_id}/FeedbackRest', [TicketFeedbackController::class, 'listarRest'])->name('tickets.listarFeedbackRest');
        Route::post('/{ticket_id}/FeedbackRest', [TicketFeedbackController::class,'agregarRest'])->name('tickets.agregarFeedbackRest');
    });
});

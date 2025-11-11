<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProyectoController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\TicketFeedbackController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\DashboardController;

// Vista de login
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/login', [AuthController::class, 'iniciarSesion']);
Route::post('/logout', [AuthController::class, 'cerrarSesion'])->name('logout');

Route::middleware(['auth'])->group(function () {
    // Ruta del Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

Route::middleware(['auth', 'permiso'])->group(function () {

    // Rutas de Perfiles
    Route::prefix('perfiles')->group(function () {
        Route::get('/', [PerfilController::class, 'gestor'])->name('perfiles.gestor');
        Route::get('/listar', [PerfilController::class, 'listarRest'])->name('perfiles.listarRest');
        Route::post('/agregar', [PerfilController::class, 'agregarRest'])->name('perfiles.agregarRest');
        Route::put('/editar/{perfil_id}', [PerfilController::class, 'editarRest'])->name('perfiles.editarRest');
        Route::patch('/eliminar/{perfil_id}', [PerfilController::class, 'eliminarRest'])->name('perfiles.eliminarRest');
    });

    // Rutas de Usuarios
    Route::prefix('usuarios')->group(function () {
        Route::get('/', [UsuarioController::class, 'gestor'])->name('usuarios.gestor');
        Route::get('/listarRest', [UsuarioController::class, 'listarRest'])->name('usuarios.listarRest');
        Route::post('/agregarRest', [UsuarioController::class, 'agregarRest'])->name('usuarios.agregarRest');
        Route::patch('/editarRest/{id}', [UsuarioController::class, 'editarRest'])->name('usuarios.editarRest');
        Route::patch('/eliminarRest/{id}', [UsuarioController::class, 'eliminarRest'])->name('usuarios.eliminarRest');
        Route::patch('/activarRest/{id}', [UsuarioController::class, 'activarRest'])->name('usuarios.activarRest');
    });

    // Rutas de Proyectos
    Route::prefix('proyectos')->controller(ProyectoController::class)->group(function () {
        Route::get('/', 'gestor')->name('proyectos.gestor');
        Route::get('/listado', 'listarRest')->name('proyectos.listado');
        Route::post('/', 'registrarRest')->name('proyectos.registrar');
        Route::patch('/{id}', 'actualizarRest')->name('proyectos.actualizar');
        Route::delete('/{id}', 'eliminarRest')->name('proyectos.eliminar');
        Route::patch('/{id}/status', 'cambiarStatusRest')->name('proyectos.activar');
        Route::get('/{id}/logs', 'logsRest')->name('proyectos.logs');
        Route::get('/{id}/usuarios', 'usuariosRest')->name('proyectos.usuarios');
    });

    // Rutas de Clientes
    Route::prefix('clientes')->controller(ClienteController::class)->group(function () {
        Route::get('/', 'gestor')->name('clientes.gestor');
        Route::get('/listado', 'listarRest')->name('clientes.listado');
        Route::post('/', 'registrarRest')->name('clientes.registro');
        Route::patch('/{id}', 'actualizarRest')->name('clientes.actualizacion');
        Route::delete('/{id}', 'eliminarRest')->name('clientes.eliminacion');
        Route::patch('/{id}/status', 'cambiarStatusRest')->name('clientes.cambio');
    });

    // Rutas de Tickets
    Route::prefix('tickets')->group(function () {
        Route::get('/', [TicketController::class, 'gestor'])->name('tickets.gestor');
        Route::get('/listado-rest', [TicketController::class, 'listarRest'])->name('tickets.listarRest');
        Route::get('{id}/detalle-rest', [TicketController::class, 'obtenerRest'])->name('tickets.obtenerRest');
        Route::post('/registro-rest', [TicketController::class, 'agregarRest'])->name('tickets.agregarRest');
        Route::patch('/{id}/edicion-rest', [TicketController::class, 'editarRest'])->name('tickets.editarRest');
        Route::patch('/{id}/status-rest', [TicketController::class, 'editarStatusRest'])->name('tickets.editarStatusRest');
        Route::patch('/{id}/prioridad-rest', [TicketController::class, 'editarPrioridadRest'])->name('tickets.editarPrioridadRest');
        Route::patch('/{id}/asignacion-rest', [TicketController::class, 'editarAsignacionRest'])->name('tickets.editarAsignacionRest');

        // Feedback
        Route::get('/{ticket_id}/feedback-rest', [TicketFeedbackController::class, 'listarRest'])->name('tickets.listarFeedbackRest');
        Route::post('/{ticket_id}/feedback-rest', [TicketFeedbackController::class, 'agregarRest'])->name('tickets.agregarFeedbackRest');
    });
});

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

// Autenticación
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/login', [AuthController::class, 'iniciarSesion']);
Route::post('/logout', [AuthController::class, 'cerrarSesion'])->name('logout');

// Rutas del Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware('auth')
    ->name('dashboard');

// Rutas de Perfiles
Route::prefix('perfiles')->middleware('auth')->group(function () {
    Route::get('/', [PerfilController::class, 'gestor'])
        ->middleware('permiso:ver_perfiles')
        ->name('perfiles.gestor');

    Route::post('/', [PerfilController::class, 'agregar'])
        ->middleware('permiso:crear_perfiles')
        ->name('perfiles.agregar');

    Route::put('/{perfil_id}', [PerfilController::class, 'editar'])
        ->middleware('permiso:editar_perfiles')
        ->name('perfiles.editar');

    Route::patch('/eliminar/{perfil_id}', [PerfilController::class, 'eliminar'])
        ->middleware('permiso:eliminar_perfiles')
        ->name('perfiles.eliminar');
});

// Rutas de Usuarios
Route::prefix('usuarios')->middleware('auth')->group(function () {
    Route::get('/', [UsuarioController::class, 'gestor'])
        ->middleware('permiso:ver_usuarios')
        ->name('usuarios.gestor');

    Route::get('/listarRest', [UsuarioController::class, 'listarRest'])
        ->middleware('permiso:ver_usuarios')
        ->name('usuarios.listarRest');

    Route::post('/agregarRest', [UsuarioController::class, 'agregarRest'])
        ->middleware('permiso:crear_usuarios')
        ->name('usuarios.agregarRest');

    Route::patch('/editarRest/{id}', [UsuarioController::class, 'editarRest'])
        ->middleware('permiso:editar_usuarios')
        ->name('usuarios.editarRest');

    Route::patch('/eliminarRest/{id}', [UsuarioController::class, 'eliminarRest'])
        ->middleware('permiso:eliminar_usuarios')
        ->name('usuarios.eliminarRest');

    Route::patch('/activarRest/{id}', [UsuarioController::class, 'activarRest'])
        ->middleware('permiso:activar_usuarios')
        ->name('usuarios.activarRest');
});

// Rutas de Proyectos
Route::prefix('proyectos')->controller(ProyectoController::class)->middleware('auth')->group(function () {
    Route::get('/', 'gestor')
        ->middleware('permiso:ver_proyectos')
        ->name('proyectos.gestor');

    Route::get('/listado', 'listarRest')
        ->middleware('permiso:ver_proyectos')
        ->name('proyectos.listado');

    Route::post('/', 'registrarRest')
        ->middleware('permiso:crear_proyectos')
        ->name('proyectos.registrar');

    Route::patch('/{id}', 'actualizarRest')
        ->middleware('permiso:editar_proyectos')
        ->name('proyectos.actualizar');

    Route::delete('/{id}', 'eliminarRest')
        ->middleware('permiso:eliminar_proyectos')
        ->name('proyectos.eliminar');

    Route::patch('/{id}/status', 'cambiarStatusRest')
        ->middleware('permiso:cambiar_status_proyectos')
        ->name('proyectos.activar');

    Route::get('/{id}/logs', 'logsRest')
        ->middleware('permiso:ver_logs_proyectos')
        ->name('proyectos.logs');

    Route::get('/{id}/usuarios', 'usuariosRest')
        ->middleware('permiso:ver_usuarios_proyectos')
        ->name('proyectos.usuarios');
});

// Rutas de Clientes
Route::prefix('clientes')->controller(ClienteController::class)->middleware('auth')->group(function () {
    Route::get('/', 'gestor')
        ->middleware('permiso:ver_clientes')
        ->name('clientes.gestor');

    Route::get('/listado', 'listarRest')
        ->middleware('permiso:ver_clientes')
        ->name('clientes.listado');

    Route::post('/', 'registrarRest')
        ->middleware('permiso:crear_clientes')
        ->name('clientes.registro');

    Route::patch('/{id}', 'actualizarRest')
        ->middleware('permiso:editar_clientes')
        ->name('clientes.actualizacion');

    Route::delete('/{id}', 'eliminarRest')
        ->middleware('permiso:eliminar_clientes')
        ->name('clientes.eliminacion');

    Route::patch('/{id}/status', 'cambiarStatusRest')
        ->middleware('permiso:cambiar_status_clientes')
        ->name('clientes.cambio');
});

// Rutas de Tickets
Route::prefix('tickets')->middleware('auth')->group(function () {
    Route::get('/', [TicketController::class, 'gestor'])
        ->middleware('permiso:ver_tickets')
        ->name('tickets.gestor');

    Route::get('/listado-rest', [TicketController::class, 'listarRest'])
        ->middleware('permiso:ver_tickets')
        ->name('tickets.listarRest');

    Route::get('{id}/detalle-rest', [TicketController::class, 'obtenerRest'])
        ->middleware('permiso:ver_tickets')
        ->name('tickets.obtenerRest');

    Route::post('/registro-rest', [TicketController::class, 'agregarRest'])
        ->middleware('permiso:crear_tickets')
        ->name('tickets.agregarRest');

    Route::patch('/{id}/edicion-rest', [TicketController::class, 'editarRest'])
        ->middleware('permiso:editar_tickets')
        ->name('tickets.editarRest');

    Route::patch('/{id}/status-rest', [TicketController::class, 'editarStatusRest'])
        ->middleware('permiso:cambiar_status_tickets')
        ->name('tickets.editarStatusRest');

    Route::patch('/{id}/prioridad-rest', [TicketController::class, 'editarPrioridadRest'])
        ->middleware('permiso:cambiar_prioridad_tickets')
        ->name('tickets.editarPrioridadRest');

    Route::patch('/{id}/asignacion-rest', [TicketController::class, 'editarAsignacionRest'])
        ->middleware('permiso:asignar_tickets')
        ->name('tickets.editarAsignacionRest');

    // Rutas de Feedback
    Route::get('/{ticket_id}/feedback-rest', [TicketFeedbackController::class, 'listarRest'])
        ->middleware('permiso:ver_feedback_tickets')
        ->name('tickets.listarFeedbackRest');

    Route::post('/{ticket_id}/feedback-rest', [TicketFeedbackController::class, 'agregarRest'])
        ->middleware('permiso:agregar_feedback_tickets')
        ->name('tickets.agregarFeedbackRest');
});
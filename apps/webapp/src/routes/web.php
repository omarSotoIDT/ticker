<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProyectoController;

Route::get('/login', function () { return view('auth.login'); });
Route::post('/login', [AuthController::class, 'iniciarSesion']);


// ======= VISTA PRINCIPAL =======
Route::get('/proyectos/gestor', [ProyectoController::class, 'gestor'])->name('proyectos.gestor');

// ======= ENDPOINTS REST =======

// Listar proyectos
Route::get('/proyectos/listado', [ProyectoController::class, 'listarRest'])->name('proyectos.listado');

// Registrar nuevo proyecto
Route::post('/proyectos/registrar', [ProyectoController::class, 'registrarRest'])->name('proyectos.registrar');

// Actualizar proyecto
Route::patch('/proyectos/{id}/actualizar', [ProyectoController::class, 'actualizarRest'])->name('proyectos.actualizar');

// Eliminar proyecto (lógico)
Route::patch('/proyectos/{id}/eliminar', [ProyectoController::class, 'eliminarRest'])->name('proyectos.eliminar');

// Activar proyecto
Route::patch('/proyectos/{id}/activar', [ProyectoController::class, 'activarRest'])->name('proyectos.activar');

// Obtener logs del proyecto
Route::get('/proyectos/{id}/logs', [ProyectoController::class, 'logsRest'])->name('proyectos.logs');

// Listar usuarios asignados al proyecto
Route::get('/proyectos/{id}/usuarios', [ProyectoController::class, 'usuariosRest'])->name('proyectos.usuarios');

// ======= Auxiliares =======
Route::get('/clientes/listado', [ClienteController::class, 'listarRest'])->name('clientes.listado');
Route::get('/usuarios/listado', [UsuarioController::class, 'listarRest'])->name('usuarios.listado');

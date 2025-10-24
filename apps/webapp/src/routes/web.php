<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProyectoController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\UsuarioController;



Route::get('/login', function () { return view('auth.login'); });
Route::post('/login', [AuthController::class, 'iniciarSesion']);


// ======= RUTAS DE PROYECTOS (protegidas) =======
Route::middleware('auth')->group(function () {
    // VISTA PRINCIPAL
    Route::get('/proyectos', [ProyectoController::class, 'gestor'])->name('proyectos.gestor');

    // ENDPOINTS REST
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
});




Route::prefix('clientes')->controller(ClienteController::class)->group(function () {
    Route::get('/', 'gestor')->name('clientes.gestor');
    Route::get('/listado', 'listarRest')->name('clientes.listado');
    Route::post('/', 'registrarRest')->name('clientes.registrar');
    Route::patch('/{id}', 'actualizarRest')->name('clientes.actualizar');
    Route::patch('/{id}/eliminar', 'eliminarRest')->name('clientes.eliminar');
    Route::patch('/{id}/activar', 'activarRest')->name('clientes.activar');
});


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
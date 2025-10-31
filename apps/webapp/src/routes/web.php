<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProyectoController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\ProyectoController;
use App\Http\Controllers\ClienteController;

Route::get('/login', function () { return view('auth.login'); });

Route::get('/login', function () { return view('auth.login'); })->name('login');
Route::post('/login', [AuthController::class, 'iniciarSesion']);
Route::post('/logout', [AuthController::class, 'cerrarSesion'])->name('logout');

Route::get('/dashboard', function () { return view('dashboard'); })->middleware('auth')->name('dashboard');

Route::prefix('perfiles')->middleware('auth')->group(function () {
    Route::get('/', [PerfilController::class, 'index'])->name('perfiles.index');
    Route::post('/', [PerfilController::class, 'guardar'])->name('perfiles.guardar');
    Route::put('/{perfil_id}', [PerfilController::class, 'actualizar'])->name('perfiles.actualizar');
    Route::patch('/eliminar/{perfil_id}', [PerfilController::class, 'eliminar'])->name('perfiles.eliminar');
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

Route::prefix('proyectos')->controller(ProyectoController::class)->group(function () {
    Route::middleware('auth')->group(function () {
        Route::get('/', 'gestor')->name('proyectos.gestor');
        Route::get('/listado', 'listarRest')->name('proyectos.listado');
        Route::post('/', 'registrarRest')->name('proyectos.registrar');
        Route::patch('/{id}', 'actualizarRest')->name('proyectos.actualizar');
        Route::delete('/{id}', 'eliminarRest')->name('proyectos.eliminar');
        Route::patch('/{id}/status', 'cambiarStatusRest')->name('proyectos.activar');
        Route::get('/{id}/logs', 'logsRest')->name('proyectos.logs');
        Route::get('/{id}/usuarios', 'usuariosRest')->name('proyectos.usuarios');
    });
});
      
Route::prefix('clientes')->controller(ClienteController::class)->group(function () {
    Route::middleware('auth')->group(function () {
        Route::get('/', 'gestor')->name('clientes.gestor');
        Route::get('/listado', 'listarRest')->name('clientes.listado');
        Route::post('/', 'registrarRest')->name('clientes.registro');
        Route::patch('/{id}', 'actualizarRest')->name('clientes.actualizacion');
        Route::delete('/{id}', 'eliminarRest')->name('clientes.eliminacion');
        Route::patch('/{id}/status', 'cambiarStatusRest')->name('clientes.cambio');
    });
});

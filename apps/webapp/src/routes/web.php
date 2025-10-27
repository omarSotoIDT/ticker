<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProyectoController;

Route::get('/login', function () { return view('auth.login'); });
Route::post('/login', [AuthController::class, 'iniciarSesion']);

Route::prefix('proyectos')->controller(ProyectoController::class)->group(function () {
    Route::get('/', 'gestor')->name('proyectos.gestor');
    Route::get('/listado', 'listarRest')->name('proyectos.listado');
    Route::post('/registrar', 'registrarRest')->name('proyectos.registrar');
    Route::patch('/{id}/actualizar', 'actualizarRest')->name('proyectos.actualizar');
    Route::patch('/{id}/eliminar', 'eliminarRest')->name('proyectos.eliminar');
    Route::patch('/{id}/activar', 'activarRest')->name('proyectos.activar');
    Route::get('/{id}/logs', 'logsRest')->name('proyectos.logs');
    Route::get('/{id}/usuarios', 'usuariosRest')->name('proyectos.usuarios');
});

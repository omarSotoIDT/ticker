<?php

namespace App\Services;

use App\RD\PerfilesRD;
use App\RA\PerfilesRA;
use App\BO\PerfilesBO;

class PerfilesService
{
    public static function obtenerPerfiles(array $filtros = [])
    {
        return PerfilesRD::obtenerPerfiles($filtros);
    }

    public static function obtenerPermisos()
    {
        return PerfilesRD::obtenerPermisos();
    }

    public static function crearPerfil(array $datos)
    {
        $bo = new PerfilesBO();
        $datosAdaptados = [
            'clave' => $datos['clave'],
            'nombre' => $datos['nombre'],
            'descripcion' => $datos['descripcion'],
            'status' => $datos['status'],
            'super_usuario' => 0, 
            'registro_autor_id' => session('user_id') ?? 1,
            'registro_fecha' => now(),
            'permisos' => $datos['permisos'] ?? []
        ];
        $perfil_id = PerfilesRA::crearPerfil($datosAdaptados);
        PerfilesRA::sincronizarPermisos($perfil_id, $datosAdaptados['permisos']);
        return $perfil_id;
    }

    public static function actualizarPerfil($perfil_id, array $datos)
    {
        $bo = new PerfilesBO();
        $datosAdaptados = [
            'clave' => $datos['clave'],
            'nombre' => $datos['nombre'],
            'descripcion' => $datos['descripcion'],
            'status' => $datos['status'],
            'super_usuario' => 0, 
            'actualizacion_autor_id' => session('user_id') ?? 1,
            'actualizacion_fecha' => now(),
            'permisos' => $datos['permisos'] ?? []
        ];
        PerfilesRA::actualizarPerfil($perfil_id, $datosAdaptados);
        PerfilesRA::sincronizarPermisos($perfil_id, $datosAdaptados['permisos']);
        return true;
    }

    public static function eliminarPerfil($perfil_id)
    {
        $datos = [
            'status' => 'ELIMINADO',
            'actualizacion_autor_id' => session('user_id') ?? 1,
            'actualizacion_fecha' => now()
        ];
        PerfilesRA::actualizarPerfil($perfil_id, $datos);
        return true;
    }

    public static function validarDatosPerfil(array $datos)
    {

    }
}
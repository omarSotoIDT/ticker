<?php

namespace App\Coordinators;

use App\Services\PerfilesService;

class PerfilesCoordinator
{
    public static function obtenerPerfiles(array $filtros = [])
    {
        $servicio = new PerfilesService();
        return $servicio->obtenerPerfiles($filtros);
    }

    public static function obtenerPermisos()
    {
        $servicio = new PerfilesService();
        return $servicio->obtenerPermisos();
    }

    public static function crearPerfil(array $datos)
    {
        $servicio = new PerfilesService();
        $servicio->validarDatosPerfil($datos);
        return $servicio->crearPerfil($datos);
    }

    public static function actualizarPerfil($perfil_id, array $datos)
    {
        $servicio = new PerfilesService();
        $servicio->validarDatosPerfil($datos);
        return $servicio->actualizarPerfil($perfil_id, $datos);
    }

    public static function eliminarPerfil($perfil_id)
    {
        $servicio = new PerfilesService();
        return $servicio->eliminarPerfil($perfil_id);
    }

    private static function validarDatosPerfil(array $datos)
    {
        $camposRequeridos = ['clave', 'nombre', 'descripcion', 'status', 'super_usuario', 'permisos'];
        foreach ($camposRequeridos as $campo) {
            if (!isset($datos[$campo]) || empty($datos[$campo])) {
                throw new \Exception("El campo '$campo' es requerido");
            }
        }
        if (!in_array($datos['status'], ['ACTIVO', 'ELIMINADO'])) {
            throw new \Exception('El status debe ser ACTIVO o ELIMINADO');
        }
        if (!is_numeric($datos['super_usuario']) || ($datos['super_usuario'] !== 0 && $datos['super_usuario'] !== 1)) {
            throw new \Exception('Super_usuario debe ser 0 o 1');
        }
    }
}
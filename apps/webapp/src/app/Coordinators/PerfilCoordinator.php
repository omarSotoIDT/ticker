<?php

namespace App\Coordinators;

use App\Services\PerfilService;

class PerfilCoordinator
{
    public static function obtenerPerfiles(array $filtros = [])
    {
        return PerfilService::obtenerPerfiles($filtros);
    }

    public static function obtenerPermisos()
    {
        return PerfilService::obtenerPermisos();
    }

    public static function crearPerfil(array $datos)
    {
        PerfilService::validarDatosPerfil($datos);
        return PerfilService::crearPerfil($datos);
    }

    public static function actualizarPerfil($perfil_id, array $datos)
    {
        PerfilService::validarDatosPerfil($datos);
        return PerfilService::actualizarPerfil($perfil_id, $datos);
    }

    public static function eliminarPerfil($perfil_id)
    {
        return PerfilService::eliminarPerfil($perfil_id);
    }

    private static function validarDatosPerfil(array $datos)
    {
        $camposRequeridos = ['clave', 'nombre', 'descripcion', 'status', 'permisos'];
        foreach ($camposRequeridos as $campo) {
            if (!isset($datos[$campo]) || empty($datos[$campo])) {
                throw new \Exception("El campo '$campo' es requerido");
            }
        }
        if (!in_array($datos['status'], ['ACTIVO', 'ELIMINADO'])) {
            throw new \Exception('El status debe ser ACTIVO o ELIMINADO');
        }
    }
}
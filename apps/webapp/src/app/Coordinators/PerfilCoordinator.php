<?php

namespace App\Coordinators;

use App\Services\PerfilService;
use App\Services\PermisoService;
use Illuminate\Support\Facades\DB;

class PerfilCoordinator
{
    public static function obtenerPerfiles(array $filtros = [])
    {
        return PerfilService::obtenerPerfiles($filtros);
    }

    public static function obtenerPermisos()
    {
        return PermisoService::obtenerPermisos();
    }

    public static function crearPerfil(array $datos)
    {
        $permisos = $datos['permisos'];

        return DB::transaction(function () use ($datos, $permisos) {
            $perfil_id = PerfilService::crearPerfil($datos);
            PermisoService::agregarPermisos($perfil_id, $permisos);
            return $perfil_id;
        });
    }

    public static function actualizarPerfil($perfil_id, array $datos)
    {
        $permisos = $datos['permisos'];

        return DB::transaction(function () use ($perfil_id, $datos, $permisos) {
            PerfilService::actualizarPerfil($perfil_id, $datos);
            PermisoService::agregarPermisos($perfil_id, $permisos);
        });
    }

    public static function eliminarPerfil($perfil_id)
    {
        return PerfilService::eliminarPerfil($perfil_id);
    }
}
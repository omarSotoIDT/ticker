<?php

namespace App\Coordinators;

use App\Services\PerfilService;
use App\Services\PermisoService;
use Illuminate\Support\Facades\DB;

class PerfilCoordinator
{
    public static function obtenerPerfiles(array $filtros = [])
    {
        $perfiles = PerfilService::obtenerPerfiles($filtros);
        $perfilIds = $perfiles->pluck('perfil_id')->toArray();

        $permisosPorPerfil = PermisoService::obtenerPermisosPerfiles($perfilIds);

        // Se agregan dińámicamente los permisos a cada perfil en base a las consultas hechas por los Repo
        $perfiles->getCollection()->transform(function ($perfil) use ($permisosPorPerfil) {
            $perfil->permisos = $permisosPorPerfil
                ->get($perfil->perfil_id, collect())
                ->pluck('permiso_id')
                ->toArray();
            return $perfil;
        });

        return [
            'perfiles' => $perfiles->items(),
            'links' => $perfiles->linkCollection(),
            'permisos' => PermisoService::obtenerPermisos(),
        ];
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
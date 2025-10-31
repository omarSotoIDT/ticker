<?php

namespace App\Services;

use App\BO\PermisoBO;
use App\RepoAction\PermisoRepoAction;
use App\RepoData\PermisoRepoData;

class PermisoService
{

    public static function prepararPermisos(int $perfil_id, array $permisoIds): array
    {
        return PermisoBO::prepararPermisos($perfil_id, $permisoIds);
    }

    public static function agregarPermisos(int $perfil_id, array $permisoIds): void
    {
        $rows = self::prepararPermisos($perfil_id, $permisoIds);
        PermisoRepoAction::agregarPermisos($perfil_id, $rows);
    }

    public static function obtenerPermisos()
    {
        return PermisoRepoData::obtenerPermisos();
    }
}
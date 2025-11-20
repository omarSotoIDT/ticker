<?php

namespace App\Services;

use App\RepoData\PerfilRepoData;
use App\RepoAction\PerfilRepoAction;
use App\BO\PerfilBO;

class PerfilService
{
    public static function obtenerPerfiles(array $filtros = [], int $limit = 10, bool $paginate = true)
    {
        return PerfilRepoData::obtenerPerfiles($filtros, $limit, $paginate);
    }

    public static function crearPerfil(array $datos)
    {
        $datosPerfil = PerfilBO::armarInsert($datos);
        unset($datosPerfil['permisos']);
        $perfil_id = PerfilRepoAction::crearPerfil($datosPerfil);
        return $perfil_id;
    }

    public static function actualizarPerfil(int $perfil_id, array $datos)
    {
        $datosPerfil = PerfilBO::armarUpdate($datos);
        unset($datosPerfil['permisos']);
        PerfilRepoAction::actualizarPerfil($perfil_id, $datosPerfil);
    }

    public static function eliminarPerfil(int $perfil_id)
    {
        $datosArmados = PerfilBO::armarDelete();
        return PerfilRepoAction::actualizarPerfil($perfil_id, $datosArmados);
    }
}
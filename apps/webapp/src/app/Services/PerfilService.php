<?php

namespace App\Services;

use App\RepoData\PerfilRepoData;
use App\RepoAction\PerfilRepoAction;
use App\BO\PerfilBO;

class PerfilService
{
    public static function obtenerPerfiles(array $filtros = [])
    {
        return PerfilRepoData::obtenerPerfiles($filtros);
    }

    public static function obtenerPermisos()
    {
        return PerfilRepoData::obtenerPermisos();
    }

    public static function crearPerfil(array $datos)
    {
        $datosPerfil = PerfilBO::armarInsert($datos);

        $permisos = $datosPerfil['permisos'] ?? [];
        unset($datosPerfil['permisos']);

        $perfil_id = PerfilRepoAction::crearPerfil($datosPerfil);

        if (!empty($permisos)) {
            $permisosListos = PerfilBO::prepararPermisos($perfil_id, $permisos);
            PerfilRepoAction::actualizarPerfil($perfil_id, [], $permisosListos);
        }

        return $perfil_id;
    }

    public static function actualizarPerfil(int $perfil_id, array $datos)
    {
        $datosPerfil = PerfilBO::armarUpdate($datos);

        $permisos = $datosPerfil['permisos'] ?? [];
        unset($datosPerfil['permisos']);

        $permisosListos = !empty($permisos) ? PerfilBO::prepararPermisos($perfil_id, $permisos) : [];

        PerfilRepoAction::actualizarPerfil($perfil_id, $datosPerfil, $permisosListos);
    }

    public static function eliminarPerfil(int $perfil_id)
    {
        $datosArmados = PerfilBO::armarDelete();

        return PerfilRepoAction::actualizarPerfil($perfil_id, $datosArmados, []);
    }

    public static function validarDatosPerfil(array $datos)
    {
        
    }
}

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
        $datosArmados = PerfilBO::armarInsert($datos);
        return PerfilRepoAction::crearPerfil($datosArmados);
    }

    public static function actualizarPerfil($perfil_id, array $datos)
    {
        $datosArmados = PerfilBO::armarUpdate($datos);
        return PerfilRepoAction::actualizarPerfil($perfil_id, $datosArmados);
    }

    public static function eliminarPerfil($perfil_id)
    {
        $datosArmados = PerfilBO::armarDelete();
        return PerfilRepoAction::actualizarPerfil($perfil_id, $datosArmados);

    }

    public static function validarDatosPerfil(array $datos)
    {
        
    }
}

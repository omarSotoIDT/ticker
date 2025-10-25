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
        $datosAdaptados = PerfilBO::armarInsert($datos);

        $perfil_id = PerfilRepoAction::crearPerfil($datosAdaptados);

        return $perfil_id;
    }

    public static function actualizarPerfil($perfil_id, array $datos)
    {
        $datosAdaptados = PerfilBO::armarUpdate($datos);

        PerfilRepoAction::actualizarPerfil($perfil_id, $datosAdaptados);

        return true;
    }

    public static function eliminarPerfil($perfil_id)
    {
        $datos = [
            'status' => 'ELIMINADO',
            'actualizacion_autor_id' => session('user_id'),
            'actualizacion_fecha' => now()
        ];
        PerfilRepoAction::actualizarPerfil($perfil_id, $datos);
        return true;
    }

    public static function validarDatosPerfil(array $datos)
    {

    }
}
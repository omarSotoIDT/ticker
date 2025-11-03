<?php

namespace App\Services;

use App\BO\UsuarioBO;
use App\RepoAction\UsuarioRepoAction;
use App\RepoData\UsuarioRepoData;

class UsuarioService
{
    public static function listar($filtros = [], $columnas = '', $orden = [], $limit = null, $offset = null) {
        $usuarios = UsuarioRepoData::listar($filtros, $columnas, $orden, $limit, $offset);
        return $usuarios;
    }

    public static function obtener($id, $columnas = '') {
        return UsuarioRepoData::obtener($id, $columnas);
    }

    public static function agregar($datos) {
        $insertUsuario = UsuarioBO::armarInsert($datos);
        return UsuarioRepoAction::crear($insertUsuario);
    }

    public static function editar($id, $datos) {
        $updateUsuario = UsuarioBO::armarUpdate($datos);
        return UsuarioRepoAction::actualizar($id, $updateUsuario);
    }

    public static function eliminar($id, $datos) {
        $deleteUsuario = UsuarioBO::armarDelete($datos);
        return UsuarioRepoAction::actualizar($id, $deleteUsuario);
    }

    public static function activar($id) {
        $activarUsuario = UsuarioBO::armarActivar();
        return UsuarioRepoAction::actualizar($id, $activarUsuario);
    }

    public static function marcarAcceso($id) {
        return UsuarioRepoAction::marcarAcceso($id);
    }
}

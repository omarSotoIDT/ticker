<?php

namespace App\Services;

use App\BO\UsuarioBO;
use App\RepoAction\UsuarioRepoAction;
use App\RepoData\UsuarioRepoData;
use Illuminate\Support\Facades\DB;

class UsuarioService
{
    public static function listar($filtros = [], $columnas = '', $orden = [], $limit = 10, $offset = null, $paginate = false) {
        $usuarios = UsuarioRepoData::listar($filtros, $columnas, $orden, $limit, $offset, $paginate);
        
        if ($paginate && $usuarios instanceof \Illuminate\Pagination\LengthAwarePaginator) {
            $usuarios->getCollection()->transform(function ($usuario) {
                $usuario->idPerfiles = (isset($usuario->idPerfiles) && $usuario->idPerfiles !== '') ? array_map('intval', explode(',', $usuario->idPerfiles)) : [];
                $usuario->nombrePerfiles = (isset($usuario->nombrePerfiles) && $usuario->nombrePerfiles !== '') ? explode(',', $usuario->nombrePerfiles) : [];
                return $usuario;
            });
            return $usuarios;
        } else {
            foreach ($usuarios as &$u) {
                $u->idPerfiles = (isset($u->idPerfiles) && $u->idPerfiles !== '') ? array_map('intval', explode(',', $u->idPerfiles)) : [];
                $u->nombrePerfiles = (isset($u->nombrePerfiles) && $u->nombrePerfiles !== '') ? explode(',', $u->nombrePerfiles) : [];
            }
            return $usuarios;
        }
    }

    public static function listarUsuarios($filtros = [], $columnas = '', $orden = [], $limit = null, $offset = null) {
        $usuarios = UsuarioRepoData::listarUsuarios($filtros, $columnas, $orden, $limit, $offset);
        foreach ($usuarios as &$u) {
            $u->idPerfiles = (isset($u->idPerfiles) && $u->idPerfiles !== '') ? array_map('intval', explode(',', $u->idPerfiles)) : [];
            $u->nombrePerfiles = (isset($u->nombrePerfiles) && $u->nombrePerfiles !== '') ? explode(',', $u->nombrePerfiles) : [];
        }
        return $usuarios;
    }

    public static function obtener($id, $columnas = '') {
        return UsuarioRepoData::obtener($id, $columnas);
    }
  
    public static function listarPerfiles($id, $columnas = '') {
        $perfiles = UsuarioRepoData::listarPerfiles($id, $columnas);
        return $perfiles;
    }

    public static function verificarStatusUsuario($usuario_id)
    {
        $usuario = UsuarioRepoData::obtener($usuario_id, 'status');
        if ($usuario && $usuario->status === \App\Consts\StatusConsts::ELIMINADO) {
            return false;
        }
        return true;
    }

    public static function agregar($datos) {
        return DB::transaction(function () use ($datos) {
            $insertUsuario = UsuarioBO::armarInsert($datos);
            $usuario_id = UsuarioRepoAction::crear($insertUsuario);
            $insertPerfiles = UsuarioBO::armarPerfilesInsert($usuario_id, $datos['perfiles']);
            UsuarioRepoAction::sincronizarPerfiles($usuario_id, $insertPerfiles);
            return $usuario_id;
        });
    }

    public static function editar($id, $datos) {
        return DB::transaction( function() use ($id, $datos) {
            $updateUsuario = UsuarioBO::armarUpdate($datos);
            $actualizado = UsuarioRepoAction::actualizar($id, $updateUsuario);
            $updatePerfiles = UsuarioBO::armarPerfilesInsert($id, $datos['perfiles']);
            UsuarioRepoAction::sincronizarPerfiles($id, $updatePerfiles);
            return $actualizado;
        });
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

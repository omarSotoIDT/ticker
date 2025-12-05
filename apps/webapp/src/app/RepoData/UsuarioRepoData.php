<?php

namespace App\RepoData;

use App\RH\UsuarioRH;
use Illuminate\Support\Facades\DB;

class UsuarioRepoData
{
    public static function listar($filtros, $columnas, $orden, $limit = 10, $offset = null, $paginate = false){
        $query = DB::table('sys_usuarios as su')->where('su.super_usuario', 0);
        $query->leftJoin('rel_usuarios_perfiles as rup', 'su.usuario_id', '=', 'rup.usuario_id');
        $query->leftJoin('sys_perfiles as sp', 'rup.perfil_id', '=', 'sp.perfil_id');

        UsuarioRH::agregarColumnas($query, $columnas);
        UsuarioRH::agregarFiltros($query, $filtros);
        UsuarioRH::agregarOrden($query, $orden);

        if ($paginate) {
            return $query->paginate($limit);
        } else {
            if(isset($limit)){
                $query->limit($limit);
            }
            if(isset($offset)){
                $query->offset($offset);
            }
            return $query->get()->toArray();
        }
    }

    public static function listarUsuarios($filtros, $columnas, $orden, $limit, $offset){
        $query = DB::table('sys_usuarios as su')->where('su.super_usuario', 0);;
        $query->leftJoin('rel_usuarios_perfiles as rup', 'su.usuario_id', '=', 'rup.usuario_id');
        $query->leftJoin('sys_perfiles as sp', 'rup.perfil_id', '=', 'sp.perfil_id');

        UsuarioRH::agregarColumnas($query, $columnas);
        UsuarioRH::agregarFiltros($query, $filtros);
        UsuarioRH::agregarOrden($query, $orden);

        if(isset($limit)){
            $query->limit($limit);
        }
        if(isset($offset)){
            $query->offset($offset);
        }
        return $query->get()->toArray();
    }

    public static function obtener($id, $columnas)
    {
        $query = DB::table('sys_usuarios as su');
        $query->leftJoin('rel_usuarios_perfiles as rup', 'su.usuario_id', '=', 'rup.usuario_id');
        $query->leftJoin('sys_perfiles as sp', 'rup.perfil_id', '=', 'sp.perfil_id');

        $query->where('su.usuario_id', $id);

        UsuarioRH::agregarColumnas($query, $columnas);

        return $query->first();
    }

    public static function listarPerfiles($usuario_id, $columnas) {
        $query = DB::table('rel_usuarios_perfiles as rup');
        $query->leftJoin('sys_perfiles as sp', 'rup.perfil_id', '=', 'sp.perfil_id');

        $query->where('rup.usuario_id', $usuario_id);

        return $query->get()->toArray();
    }
}

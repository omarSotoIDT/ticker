<?php

namespace App\RepoData;

use App\RH\UsuarioRH;
use Illuminate\Support\Facades\DB;

class UsuarioRepoData
{
    public static function listar($filtros, $columnas, $orden, $limit, $offset){
        $query = DB::table('sys_usuarios as su');
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

    public static function obtener($id = null, $columnas = '')
    {
        $query = DB::table('sys_usuarios as su');
        $query->leftJoin('rel_usuarios_perfiles as rup', 'su.usuario_id', '=', 'rup.usuario_id');
        $query->leftJoin('sys_perfiles as sp', 'rup.perfil_id', '=', 'sp.perfil_id');

        $query->where('su.usuario_id', $id);

        UsuarioRH::agregarColumnas($query, $columnas);

        return $query->first();
    }
}

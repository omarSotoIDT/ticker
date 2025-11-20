<?php

namespace App\RepoData;

use App\RH\PerfilRH;
use Illuminate\Support\Facades\DB;

class PerfilRepoData
{
    public static function obtenerPerfiles(array $filtros = [], $limit = 10, $paginate)
    {
        $consulta = DB::table('sys_perfiles')
            ->where('status', 'ACTIVO')
            ->where('super_usuario', 0);

        $consulta = PerfilRH::aplicarFiltros($consulta, $filtros);

        if ($paginate){
            $resultado = $consulta->paginate($limit);
        }
        else{
            $resultado = $consulta->get()->toArray();
        }
        return $resultado;
    }
}
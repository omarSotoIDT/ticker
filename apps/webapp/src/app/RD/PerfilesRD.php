<?php

namespace App\RD;

use App\RH\PerfilesRH;
use Illuminate\Support\Facades\DB;

class PerfilesRD
{
    public static function obtenerPerfiles(array $filtros = [])
    {
        $consulta = DB::table('sys_perfiles')->where('status', 'ACTIVO');
        $consulta = PerfilesRH::aplicarFiltros($consulta, $filtros);
        return $consulta->get();
    }

    public static function obtenerPermisos()
    {
        return DB::table('sys_permisos')->get();
    }
}
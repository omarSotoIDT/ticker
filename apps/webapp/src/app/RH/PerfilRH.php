<?php

namespace App\RH;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class PerfilRH
{
    public static function aplicarFiltros($consulta, array $filtros = [])
    {
        if (!empty($filtros['busqueda'])) {
            $busqueda = '%' . $filtros['busqueda'] . '%';
            $consulta->where(function ($q) use ($busqueda) {
                $q->where('sys_perfiles.clave', 'like', $busqueda)
                  ->orWhere('sys_perfiles.nombre', 'like', $busqueda)
                  ->orWhere('sys_perfiles.descripcion', 'like', $busqueda);
            });
        }
        return $consulta;
    }
}
<?php

namespace App\RH;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class PerfilesRH
{
    public static function aplicarFiltros($consulta, array $filtros = [])
    {
        if (!empty($filtros['busqueda'])) {
            $busqueda = '%' . $filtros['busqueda'] . '%';
            $consulta->where(function ($q) use ($busqueda) {
                $q->where('sys_perfiles.clave', 'like', $busqueda)
                  ->orWhere('sys_perfiles.nombre', 'like', $busqueda)
                  ->orWhere('sys_perfiles.descripcion', 'like', $busqueda)
                  ->orWhere('sys_perfiles.status', 'like', $busqueda)
                  ->orWhereExists(function ($subQuery) use ($busqueda) {
                      $subQuery->select(DB::raw(1))
                               ->from('rel_perfiles_permisos')
                               ->join('sys_permisos', 'rel_perfiles_permisos.permiso_id', '=', 'sys_permisos.permiso_id')
                               ->whereColumn('rel_perfiles_permisos.perfil_id', 'sys_perfiles.perfil_id')
                               ->where(function ($q) use ($busqueda) {
                                   $q->where('sys_permisos.titulo', 'like', $busqueda)
                                     ->orWhere('sys_permisos.codigo', 'like', $busqueda);
                               });
                  });
            });
        }
        return $consulta;
    }
}
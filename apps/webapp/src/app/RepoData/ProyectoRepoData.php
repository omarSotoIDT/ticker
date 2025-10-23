<?php

namespace App\RepoData;

use Illuminate\Support\Facades\DB;
use App\RepoHelper\ProyectoRepoHelper;

class ProyectoRepoData
{
    public static function obtenerProyectos(array $filters = [])
    {
        $query = DB::table('proyectos')
            ->select(
                'proyecto_id',
                'cliente_id',
                'nombre',
                'descripcion',
                'status',
                'registro_fecha'
            )
            ->where('status', '!=', 'ELIMINADO');

        $query = ProyectoRepoHelper::aplicarFiltros($query, $filters);

        return $query->get();
    }

    public static function obtenerPorId(int $id): ?object
    {
        return DB::table('proyectos')
            ->where('proyecto_id', $id)
            ->first();
    }

    public static function obtenerUsuariosAsignados(int $proyectoId): array
    {
        return DB::table('rel_usuarios_proyectos')
            ->where('proyecto_id', $proyectoId)
            ->where('status', 'ACTIVO')
            ->pluck('usuario_id')
            ->toArray();
    }

    public static function reasignarUsuarios(array $ids): string
    {
        if (empty($ids)) return '';
        return DB::table('sys_usuarios')
            ->whereIn('usuario_id', $ids)
            ->pluck('nombre')
            ->implode(', ');
    }


    public static function obtenerLogs(int $proyectoId)
    {
        return DB::table('log_proyectos as lp')
            ->join('proyectos as p', 'lp.proyecto_id', '=', 'p.proyecto_id')
            ->join('sys_usuarios as u', 'lp.usuario_id', '=', 'u.usuario_id')
            ->select(
                'lp.log_proyecto_id',
                'p.nombre as proyecto_nombre',
                DB::raw("CONCAT(u.nombre, ' ', u.apellido_paterno, ' ', IFNULL(u.apellido_materno, '')) as usuario_nombre"),
                'lp.descripcion',
                'lp.registro_fecha'
            )
            ->where('lp.proyecto_id', $proyectoId)
            ->orderByDesc('lp.registro_fecha')
            ->get();
    }

    public static function listarUsuariosAsignados(int $proyectoId)
    {
        return DB::table('rel_usuarios_proyectos as rup')
            ->join('sys_usuarios as u', 'rup.usuario_id', '=', 'u.usuario_id')
            ->where('rup.proyecto_id', $proyectoId)
            ->where('rup.status', 'ACTIVO')
            ->select('u.usuario_id', 'u.nombre_completo as nombre') // ← aquí
            ->get();
    }    
}

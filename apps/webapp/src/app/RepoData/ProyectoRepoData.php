<?php

namespace App\RepoData;

use Illuminate\Support\Facades\DB;
use App\RepoHelper\ProyectoRepoHelper;

class ProyectoRepoData
{
    public static function obtenerProyectos(array $filters = [], $limit = 10, $paginate = false)
    {
        $query = DB::table('proyectos as p')
            ->select(
                'p.proyecto_id',
                'p.cliente_id',
                'p.nombre',
                'p.descripcion',
                'p.status',
                'p.registro_fecha',
                'c.nombre as cliente_nombre'
            )
            ->leftJoin('clientes as c', 'p.cliente_id', '=', 'c.cliente_id')
            ->where('p.status', '!=', 'ELIMINADO');

        $query = ProyectoRepoHelper::aplicarFiltros($query, $filters);

        if ($paginate) {
            $paginator = $query->paginate($limit);
            if (!empty($filters) && is_array($filters)) {
                $paginator->appends($filters);
            }
            return $paginator;
        } else {
            return $query->get()->toArray();
        }
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
            ->pluck('usuario')
            ->implode(', ');
    }

    public static function obtenerLogs(int $proyectoId)
    {
        return DB::table('log_proyectos as lp')
            ->join('sys_usuarios as u', 'lp.usuario_id', '=', 'u.usuario_id')
            ->select(
                'lp.log_proyecto_id as id',
                'lp.descripcion as accion',
                'lp.registro_fecha as fecha',
                DB::raw("u.usuario as usuario")
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
            ->select(
                'u.usuario_id',
                'u.usuario',
                'u.email'
            )
            ->get();
    }

}
<?php

namespace App\RepoData;

use Illuminate\Support\Facades\DB;

class DashboardRepoData
{
    public static function obtenerTotales(): array
    {
        $total = DB::table('tickets')->count();

        $cerrados = DB::table('tickets')
            ->whereRaw('UPPER(status) = ?', [mb_strtoupper('Atendido')])
            ->count();

        $urgentes = DB::table('tickets')
            ->whereRaw('UPPER(prioridad) = ?', [mb_strtoupper('Urgente')])
            ->count();

        $activos = max(0, $total - $cerrados);

        return ['total' => $total, 'activos' => $activos, 'cerrados' => $cerrados, 'urgentes' => $urgentes];
    }

    public static function contarPorEstado()
    {
        return DB::table('tickets')
            ->select(DB::raw('UPPER(status) as status'), DB::raw('count(*) as total'))
            ->groupBy(DB::raw('UPPER(status)'))
            ->get();
    }

    public static function contarPorPrioridad()
    {
        return DB::table('tickets')
            ->select(DB::raw('UPPER(prioridad) as prioridad'), DB::raw('count(*) as total'))
            ->groupBy(DB::raw('UPPER(prioridad)'))
            ->get();
    }

    public static function topClientes(int $limit = 5)
    {
        return DB::table('tickets as t')
            ->join('clientes as c', 't.cliente_id', '=', 'c.cliente_id')
            ->select('c.cliente_id', 'c.nombre', DB::raw('count(*) as total'))
            ->groupBy('c.cliente_id', 'c.nombre')
            ->orderByDesc('total')
            ->limit($limit)
            ->get();
    }
}

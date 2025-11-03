<?php

namespace App\RepoData;

use Illuminate\Support\Facades\DB;
use App\Consts\TicketConsts;

class DashboardRepoData
{
    public static function obtenerTotales(): array
    {
        $total = DB::table('tickets')->count();
        $statusCerrados = [
            TicketConsts::ATENDIDO,
            TicketConsts::CERRADO,
        ];

        $cerrados = DB::table('tickets')
            ->whereIn(DB::raw('UPPER(status)'), array_map('mb_strtoupper', $statusCerrados))
            ->count();

        $cancelados = DB::table('tickets')
            ->whereRaw('UPPER(status) = ?', [mb_strtoupper(TicketConsts::CANCELADO)])
            ->count();

        $urgentes = DB::table('tickets')
            ->whereRaw('UPPER(prioridad) = ?', [mb_strtoupper(TicketConsts::URGENTE)])
            ->count();

    $estadosExcluidos = array_merge($statusCerrados, [TicketConsts::CANCELADO]);
        $activos = DB::table('tickets')
            ->whereNotIn(DB::raw('UPPER(status)'), array_map('mb_strtoupper', $estadosExcluidos))
            ->count();

        return [
            'total' => $total,
            'activos' => (int) $activos,
            'cerrados' => (int) $cerrados,
            'cancelados' => (int) $cancelados,
            'urgentes' => (int) $urgentes,
        ];
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

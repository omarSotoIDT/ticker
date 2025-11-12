<?php

namespace App\RepoData;

use Illuminate\Support\Facades\DB;
use App\Consts\TicketConsts;

class DashboardRepoData
{
    public static function obtenerDatosDashboard(): object
    {
        $statusCerrados = [
            TicketConsts::ATENDIDO,
            TicketConsts::CERRADO,
        ];

        return DB::table('tickets')->selectRaw('
            COUNT(*) AS total,
            SUM(CASE WHEN UPPER(status) NOT IN (?, ?, ?) THEN 1 ELSE 0 END) AS activos,
            SUM(CASE WHEN UPPER(status) IN (?, ?) THEN 1 ELSE 0 END) AS cerrados,
            SUM(CASE WHEN UPPER(status) = ? THEN 1 ELSE 0 END) AS cancelados,
            SUM(CASE WHEN UPPER(prioridad) = ? THEN 1 ELSE 0 END) AS urgentes
        ', [
            mb_strtoupper(TicketConsts::ATENDIDO),
            mb_strtoupper(TicketConsts::CERRADO),
            mb_strtoupper(TicketConsts::CANCELADO),

            mb_strtoupper(TicketConsts::ATENDIDO),
            mb_strtoupper(TicketConsts::CERRADO),

            mb_strtoupper(TicketConsts::CANCELADO),
            mb_strtoupper(TicketConsts::URGENTE),
        ])->first();
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
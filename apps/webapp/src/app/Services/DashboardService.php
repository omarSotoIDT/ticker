<?php

namespace App\Services;

use App\RepoData\DashboardRepoData;
use App\BO\DashboardBO;

class DashboardService
{
    public static function obtenerCards(): array
    {
        $data = DashboardRepoData::obtenerDatosDashboard();

        $totales = [
            'total' => (int) ($data->total),
            'activos' => (int) ($data->activos),
            'cerrados' => (int) ($data->cerrados),
            'cancelados' => (int) ($data->cancelados),
            'urgentes' => (int) ($data->urgentes),
        ];

        return DashboardBO::armarCardsFromTotales($totales);
    }

    public static function ticketsPorEstado(): array
    {
        $rows = DashboardRepoData::contarPorEstado();
        return DashboardBO::armarDataset($rows->toArray(), 'status', 'total');
    }

    public static function ticketsPorPrioridad(): array
    {
        $rows = DashboardRepoData::contarPorPrioridad();
        return DashboardBO::armarDataset($rows->toArray(), 'prioridad', 'total');
    }

    public static function topClientesPorTickets(int $limit = 5): array
    {
        $rows = DashboardRepoData::topClientes($limit);
        return DashboardBO::armarDataset($rows->toArray(), 'nombre', 'total');
    }
}

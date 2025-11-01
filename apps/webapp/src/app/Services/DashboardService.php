<?php

namespace App\Services;

use App\RepoData\DashboardRepoData;
use App\BO\DashboardBO;

class DashboardService
{
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

    public static function obtenerCards(): array
    {
        $totales = DashboardRepoData::obtenerTotales();
        return DashboardBO::armarCardsFromTotales($totales);
    }
}


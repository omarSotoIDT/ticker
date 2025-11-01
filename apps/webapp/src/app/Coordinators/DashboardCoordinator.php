<?php

namespace App\Coordinators;

use App\Services\DashboardService;

class DashboardCoordinator
{
    public static function obtenerDatosDashboard(): array
    {
        return [
            'ticketsPorEstado' => DashboardService::ticketsPorEstado(),
            'ticketsPorPrioridad' => DashboardService::ticketsPorPrioridad(),
            'topClientes' => DashboardService::topClientesPorTickets(5),
            'cards' => DashboardService::obtenerCards(),
        ];
    }
}

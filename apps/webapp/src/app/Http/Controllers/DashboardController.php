<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Coordinators\DashboardCoordinator;

class DashboardController extends Controller
{
    public function index()
    {
        $datos = DashboardCoordinator::obtenerDatosDashboard();

        return view('dashboard.dashboard', [
            'ticketsPorEstado' => $datos['ticketsPorEstado'],
            'ticketsPorPrioridad' => $datos['ticketsPorPrioridad'],
            'topClientes' => $datos['topClientes'],
            'cards' => $datos['cards'],
        ]);
    }
}
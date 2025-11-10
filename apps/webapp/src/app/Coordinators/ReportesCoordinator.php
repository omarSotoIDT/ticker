<?php

namespace App\Coordinators;

use App\Services\ReportesService;

class ReportesCoordinator
{
    public static function obtenerResumen($tipo = 'etiqueta', $filtros = [])
    {
        return ReportesService::obtenerResumen($tipo, $filtros);
    }

    public static function reportesTickets(array $filtros = [])
    {
        return ReportesService::reportesTickets($filtros);
    }

    public static function obtenerFiltros($datos)
    {
        $tipo = $datos->get('tipo', 'cliente');
        $filtros = ReportesService::obtenerFiltros($tipo);
        return response()->json($filtros);
    }
}

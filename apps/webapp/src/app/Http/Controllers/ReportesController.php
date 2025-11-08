<?php

namespace App\Http\Controllers;

use App\Coordinators\ReportesCoordinator;
use Illuminate\Http\Request;
use App\Services\Exceptions;

class ReportesController extends Controller
{
    public function index()
    {   
        return view('reportes.Reportes');
    }

    public function obtenerDatos(Request $request)
    {
        try {
            $tipo = $request->get('tipo', 'etiqueta');
            $filtros = $this->construirFiltros($request, $tipo);
            $data = ReportesCoordinator::obtenerResumen($tipo, $filtros);
            return response()->json($data);
        } catch (\Throwable $e) {
            return Exceptions::handleException($e, 'Ocurrió un error al obtener los datos del resumen de reportes.', __METHOD__);
        }
    }

    public function listarTickets(Request $request)
    {
        try {
            $tipo = $request->input('tipo', 'etiqueta');
            $filtros = $this->construirFiltros($request, $tipo);
            $tickets = ReportesCoordinator::reportesTickets($filtros);
            return response()->json($tickets);
        } catch (\Throwable $e) {
            return Exceptions::handleException($e, 'Ocurrió un error al listar los tickets del reporte.', __METHOD__);
        }
    }

    public function obtenerFiltros(Request $request)
    {
        try {
            return ReportesCoordinator::obtenerFiltros($request);
        } catch (\Throwable $e) {
            return Exceptions::handleException($e, 'Ocurrió un error al obtener los filtros del reporte.', __METHOD__);
        }
    }


    private function construirFiltros(Request $request, string $tipo): array
    {
        $filtros = $request->except(['tipo', 'filtro']);
        $filtroValor = $request->input('filtro');

        if (!is_null($filtroValor) && $filtroValor !== '') {
            $filtros[$tipo] = $filtroValor;
        }

        return $filtros;
    }
}

<?php

namespace App\Services;

use App\Consts\TicketConsts;
use App\Services\ClienteService;
use App\Services\EtiquetaService;
use App\Services\ProyectoService;
use App\Services\TicketService;
use App\Services\UsuarioService;

class ReportesService
{
    public static function obtenerResumen($tipo = 'etiqueta', $filtros = [])
    {
        $columnasMap = [
            'etiqueta_id'         => 'etiquetaId, etiqueta',
            'proyecto_id'         => 'proyectoId, proyecto',
            'cliente_id'          => 'clienteId, cliente',
            'usuario_asignado_id' => 'usuarioAsignadoId, usuarioAsignado',
            'status'              => 'status',
        ];

        if (!isset($columnasMap[$tipo])) {
            throw new \Exception("Tipo de reporte no válido: {$tipo}");
        }

        $tickets = TicketService::listar($filtros, $columnasMap[$tipo]);

        $coleccion = collect($tickets);

        switch ($tipo) {
            case 'etiqueta_id':
                $campoAgrupar = 'etiqueta';
                $nombrePorDefecto = 'Sin etiqueta';
                break;
            case 'proyecto_id':
                $campoAgrupar = 'proyecto';
                $nombrePorDefecto = 'Sin proyecto';
                break;
            case 'cliente_id':
                $campoAgrupar = 'cliente';
                $nombrePorDefecto = 'Sin cliente';
                break;
            case 'usuario_asignado_id':
                $campoAgrupar = 'usuarioAsignado';
                $nombrePorDefecto = 'Sin usuario';
                break;
            case 'status':
                $campoAgrupar = 'status';
                $nombrePorDefecto = 'Sin estado';
                break;
        }

        $agrupado = $coleccion
            ->groupBy($campoAgrupar)
            ->map(function ($items, $key) use ($nombrePorDefecto) {
                return [
                    'nombre' => $key ?: $nombrePorDefecto,
                    'total'  => count($items),
                ];
            })
            ->values();

        return $agrupado;
    }

    public static function reportesTickets(array $filtros = [])
    {
        $columnas = 'ticketId,serieFolio,titulo,cliente,proyecto,usuarioAsignado,status,prioridad,etiqueta,registroFecha';
        $orden = ['t.registro_fecha' => 'desc'];
        return TicketService::listar($filtros, $columnas, $orden);
    }

    public static function obtenerFiltros($tipo)
    {
        switch ($tipo) {
            case 'cliente_id':
                $clientes = ClienteService::listarClientes();
                return collect($clientes)->map(fn($c) => ['valor' => $c->cliente_id, 'texto' => $c->nombre])->values();

            case 'proyecto_id':
                $data = ProyectoService::listar();
                return collect($data)->map(fn($p) => ['valor' => $p->proyecto_id, 'texto' => $p->nombre])->values();

            case 'usuario_asignado_id':
                $data = UsuarioService::listar([], 'usuarioId, usuario');
                return collect($data)->map(fn($u) => ['valor' => $u->usuarioId, 'texto' => $u->usuario])->values();

            case 'etiqueta_id':
                $data = EtiquetaService::listar([], 'etiquetaId, titulo');
                return collect($data)->map(fn($e) => ['valor' => $e->etiquetaId, 'texto' => $e->titulo])->values();

            case 'status':
                return collect([
                    ['valor' => TicketConsts::ABIERTO, 'texto' => TicketConsts::ABIERTO],
                    ['valor' => TicketConsts::EN_PROGRESO, 'texto' => TicketConsts::EN_PROGRESO],
                    ['valor' => TicketConsts::ATENDIDO, 'texto' => TicketConsts::ATENDIDO],
                    ['valor' => TicketConsts::CERRADO, 'texto' => TicketConsts::CERRADO],
                    ['valor' => TicketConsts::INFO_REQUERIDA, 'texto' => TicketConsts::INFO_REQUERIDA],
                    ['valor' => TicketConsts::CANCELADO, 'texto' => TicketConsts::CANCELADO],
                ]);

            default:
                return collect([]);
        }
    }
}
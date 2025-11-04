<?php

namespace App\Coordinators;

use App\Services\TicketService;
use App\Services\ProyectoService;
use App\Services\ClienteService;
use App\Services\UsuarioService;
use App\Services\EtiquetaService;
use App\Consts\TicketConsts;
use Illuminate\Support\Collection;

class ReportesCoordinator
{
    public static function obtenerResumen($tipo = 'etiqueta', $filtros = [])
    {
        $columnasMap = [
            'etiqueta'         => 'etiquetaId, etiqueta',
            'proyecto'         => 'proyectoId, proyecto',
            'cliente'          => 'clienteId, cliente',
            'usuario_asignado' => 'usuarioAsignadoId, usuarioAsignado',
            'status'           => 'status',
        ];

        if (!isset($columnasMap[$tipo])) {
            throw new \Exception("Tipo de reporte no válido: {$tipo}");
        }

        $tickets = TicketService::listar($filtros, $columnasMap[$tipo]);

        $coleccion = collect($tickets);

        switch ($tipo) {
            case 'etiqueta':
                $campoAgrupar = 'etiqueta';
                $nombrePorDefecto = 'Sin etiqueta';
                break;
            case 'proyecto':
                $campoAgrupar = 'proyecto';
                $nombrePorDefecto = 'Sin proyecto';
                break;
            case 'cliente':
                $campoAgrupar = 'cliente';
                $nombrePorDefecto = 'Sin cliente';
                break;
            case 'usuario_asignado':
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

        $tickets = TicketService::listar($filtros, $columnas, $orden);

        $coleccion = collect($tickets)->map(function ($t) {
            return [
                'serieFolio'     => $t->serieFolio,
                'titulo'         => $t->titulo,
                'cliente'        => $t->cliente,
                'proyecto'       => $t->proyecto,
                'asignado'       => $t->usuarioAsignado,
                'estado'         => $t->status,
                'prioridad'      => $t->prioridad,
                'etiqueta'       => $t->etiqueta,
            ];
        });

        return $coleccion->values();
    }

    public static function obtenerFiltros($datos)
    {

        $tipo = $datos->get('tipo', 'cliente');
        switch ($tipo) {

            case 'cliente':
                $clientes = ClienteService::listarClientes();
                $filtros = collect($clientes)->map(fn($c) => [
                    'valor' => $c->cliente_id,
                    'texto' => $c->nombre
                ])->values()
                ->toArray();

                break;

            case 'proyecto':
                $data = ProyectoService::listar();
                $filtros = collect($data)->map(fn($p) => [
                    'valor' => $p->proyecto_id,
                    'texto' => $p->nombre
                ])->values();
                break;

            case 'usuario_asignado':
                $data = UsuarioService::listar([], 'usuarioId, usuario');
                $filtros = collect($data)->map(fn($u) => [
                    'valor' => $u->usuarioId,
                    'texto' => $u->usuario
                ])->values();
                break;

            case 'etiqueta':
                $data = EtiquetaService::listar([], 'etiquetaId, titulo');
                $filtros = collect($data)->map(fn($e) => [
                    'valor' => $e->etiquetaId,
                    'texto' => $e->titulo
                ])->values();
                break;

            case 'status':
                $filtros = collect([
                    ['valor' => TicketConsts::ABIERTO, 'texto' => TicketConsts::ABIERTO],
                    ['valor' => TicketConsts::EN_PROGRESO, 'texto' => TicketConsts::EN_PROGRESO],
                    ['valor' => TicketConsts::ATENDIDO, 'texto' => TicketConsts::ATENDIDO],
                    ['valor' => TicketConsts::CERRADO, 'texto' => TicketConsts::CERRADO],
                    ['valor' => TicketConsts::INFO_REQUERIDA, 'texto' => TicketConsts::INFO_REQUERIDA],
                    ['valor' => TicketConsts::CANCELADO, 'texto' => TicketConsts::CANCELADO],
                ]);
                break;

            default:
                $filtros = collect([]);
        }

        return response()->json($filtros);
    }
}

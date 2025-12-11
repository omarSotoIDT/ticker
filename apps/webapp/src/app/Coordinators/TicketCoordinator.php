<?php

namespace App\Coordinators;

use App\Consts\StatusConsts;
use App\Services\ClienteService;
use App\Services\EtiquetaService;
use App\Services\FolioService;
use App\Services\ProyectoService;
use App\Services\TicketFeedbackService;
use App\Services\TicketService;
use App\Services\UsuarioService;
use App\Services\LogService;
use Exception;
use Illuminate\Support\Facades\DB;


class TicketCoordinator
{
    public static function obtenerTickets(array $filtros = [], bool $paginate = true)
    {
        $tickets = TicketService::listar(
            $filtros,
            'ticketId,cliente,proyecto,etiqueta,usuarioAsignado,folio,serieFolio,titulo,descripcion,prioridad,status,registroFecha',
            ['folio' => 'desc'],
            10,
            null,
            $paginate
        );

        $esPaginado = $paginate && $tickets instanceof \Illuminate\Pagination\LengthAwarePaginator;

        return [
            'tickets' => $esPaginado ? $tickets->items() : null,
            'tickets_sin_paginar' => $esPaginado ? null : $tickets,
            'links' => $esPaginado ? $tickets->linkCollection() : [],
        ];
    }

    public static function cargarGestor()
    {
        $resultadoTickets = self::obtenerTickets([], true);
        $tickets = $resultadoTickets['tickets'] ?? [];
        $links = $resultadoTickets['links'] ?? [];
        $etiquetas = EtiquetaService::listar(['status' => StatusConsts::ACTIVO], 'etiquetaId,titulo');
        $resultadoUsuarios = UsuarioCoordinator::obtenerUsuarios([], false);
        $usuarios = $resultadoUsuarios['usuarios_sin_paginar'];
        $resultadoProyectos = ProyectoCoordinator::obtenerProyectos(['status' => StatusConsts::ACTIVO], false);
        $proyectos = $resultadoProyectos['proyectos_sin_paginar'];
        $resultadoClientes = ClienteCoordinator::obtenerClientes(['status' => StatusConsts::ACTIVO], false);
        $clientes = $resultadoClientes['clientes_sin_paginar'];
        return ['tickets' => $tickets, 'links' => $links, 'etiquetas' => $etiquetas, 'usuarios' => $usuarios,  'proyectos' => $proyectos, 'clientes'  => $clientes];
    }

    public static function agregar(array $datos)
    {
        return DB::transaction(function () use ($datos) {
            $folio = FolioService::obtener('ticket');
            $datos['folio'] = $folio;

            $log_folio = FolioService::obtener('log_tickets');


            $ticketId = TicketService::agregar($datos);

            if (!$ticketId) {
                throw new \Exception("No se pudo crear el ticket");
            }

            $logDescripcion = "Ticket '{$datos['titulo']}' creado.";
            TicketService::agregarLog($ticketId, $log_folio, $logDescripcion);

            return $ticketId;
        });
    }

    public static function actualizarProyecto($id, $data)
    {
        $ticketAnterior = TicketService::obtener(
            $id,
            'titulo,descripcion,prioridad,status,usuarioAsignadoId,clienteId,proyectoId,etiquetaId'
        );

        if (!$ticketAnterior) {
            throw new \Exception("El ticket con ID {$id} no existe.");
        }
        $datosAnteriores = [
            'titulo' => $ticketAnterior->titulo,
            'descripcion' => $ticketAnterior->descripcion,
            'prioridad' => $ticketAnterior->prioridad,
            'status' => $ticketAnterior->status,
            'cliente' => ClienteService::obtenerNombre($ticketAnterior->clienteId),
            'proyecto' => ProyectoService::obtenerPorId($ticketAnterior->proyectoId)?->nombre,
            'etiqueta' => EtiquetaService::listar(['etiquetaId' => $ticketAnterior->etiquetaId], 'titulo')[0]->titulo,
            'usuario_asignado' => UsuarioService::obtener($ticketAnterior->usuarioAsignadoId, 'usuario')?->usuario,
        ];
        $descripcionLog = LogService::armarDescripcion($datosAnteriores, $data);
        if (!$descripcionLog) {
            return;
        }
        return DB::transaction(function () use ($id, $data, $descripcionLog) {
            $folio = FolioService::obtener('log_tickets');
            TicketService::editar($id, $data);
            TicketService::agregarLog($id, $folio, $descripcionLog);
        });
    }

    public static function editarStatus($ticketId, $datos)
    {
        $ticketActual = TicketService::obtener($ticketId, 'ticketId,status');
        if (!$ticketActual) {
            throw new \Exception("Ticket no existe");
        }

        if ($ticketActual->status === $datos['status']) {
            return false;
        }

        return DB::transaction(function () use ($ticketId, $datos, $ticketActual) {
            $folio = FolioService::obtener('log_tickets');
            TicketService::editarStatus($ticketId, $datos);
            $descripcion = "Estado cambiado de '{$ticketActual->status}' a '{$datos['status']}'";
            TicketService::agregarLog($ticketId, $folio, $descripcion);
        });
    }

    public static function editarPrioridad($ticketId, $datos)
    {
        $ticketActual = TicketService::obtener($ticketId, 'ticketId,prioridad');
        if (!$ticketActual) {
            throw new \Exception("Ticket no existe");
        }

        if ($ticketActual->prioridad === $datos['prioridad']) {
            return false;
        }
        return DB::transaction(function () use ($ticketId, $datos, $ticketActual) {
            $folio = FolioService::obtener('log_tickets');
            TicketService::editarPrioridad($ticketId, $datos);
            $descripcion = "Prioridad cambiada de '{$ticketActual->prioridad}' a '{$datos['prioridad']}'";
            TicketService::agregarLog($ticketId, $folio, $descripcion);
        });
    }

    public static function editarAsignacion(int $ticketId, array $datos)
    {
        $ticketActual = TicketService::obtener($ticketId, 'usuarioAsignadoId');
        if (!$ticketActual) {
            throw new \Exception("Ticket no existe");
        }

        if (
            !isset($datos['usuario_asignado_id']) ||
            $ticketActual->usuarioAsignadoId == $datos['usuario_asignado_id']
        ) {
            return false;
        }
        $nombreUsuarioAnterior = UsuarioService::obtener($ticketActual->usuarioAsignadoId, 'usuario')->usuario;
        $nombreUsuarioNuevo = UsuarioService::obtener($datos['usuario_asignado_id'], 'usuario')->usuario;

        return DB::transaction(function () use ($ticketId, $datos, $nombreUsuarioAnterior, $nombreUsuarioNuevo) {
            $folio = FolioService::obtener('log_tickets');
            TicketService::editarAsignacion($ticketId, $datos);
            $descripcion = "Asignación cambiada de '{$nombreUsuarioAnterior}' a '{$nombreUsuarioNuevo}'";
            TicketService::agregarLog($ticketId, $folio, $descripcion);
        });
    }


    public static function obtener($id)
    {
        $ticket = TicketService::obtener($id, 'ticketId,clienteId,cliente,proyectoId,proyecto,etiquetaId,etiqueta,usuarioAsignadoId,usuarioAsignado,serieFolio,titulo,descripcion,prioridad,status,registroFecha,actualizacionFecha');
        $ticketFeedback = TicketFeedbackService::listar(['ticket_id' => $id], 'usuario,folio,comentario,registroFecha', ['folio' => 'desc']);
        $ticketLogs = TicketService::obtenerLogs($id, 'folio,descripcion,registroFecha,usuarioId,usuario', ['folio' => 'desc']);
        return ['ticket' => $ticket, 'feedback' => $ticketFeedback, 'logs' => $ticketLogs];
    }
}

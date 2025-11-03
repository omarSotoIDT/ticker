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
use Exception;
use Illuminate\Support\Facades\DB;


class TicketCoordinator
{
    public static function cargarGestor()
    {
        $tickets = TicketService::listar([], 'ticketId,cliente,proyecto,etiqueta,usuarioAsignado,folio,serieFolio,titulo,descripcion,prioridad,status,registroFecha', ['folio' => 'desc']);
        $etiquetas = EtiquetaService::listar(['status' => StatusConsts::ACTIVO], 'etiquetaId,titulo');
        $usuarios = UsuarioService::listar(['status' => StatusConsts::ACTIVO], 'usuarioId,usuario');
        $proyectos = ProyectoService::listar(['status' => StatusConsts::ACTIVO]);
        $clientes = ClienteService::listarClientes(['status' => StatusConsts::ACTIVO]);
        return ['tickets' => $tickets, 'etiquetas' => $etiquetas, 'usuarios' => $usuarios,  'proyectos' => $proyectos, 'clientes'  => $clientes];
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

    public static function actualizarTicket(int $ticketId, array $datos)
    {
        return DB::transaction(function () use ($ticketId, $datos) {
            $ticketActual = TicketService::obtener($ticketId, 'titulo,descripcion,prioridad,status,usuarioAsignadoId,clienteId,proyectoId');
            if (!$ticketActual) {
                throw new \Exception("Ticket no existe");
            }

            $cambios = [];

            $camposComparar = ['titulo' => 'titulo', 'descripcion' => 'descripcion', 'prioridad' => 'prioridad', 'status' => 'status', 'usuario_asignado_id' => 'usuarioAsignadoId', 'cliente_id' => 'clienteId', 'proyecto_id' => 'proyectoId'];
             foreach ($camposComparar as $campo => $valor) {
                if (isset($datos[$campo]) && $datos[$campo] != $ticketActual->$valor) {
                    $cambios[] = ucfirst($campo) . " cambiado de '{$ticketActual->$valor}' a '{$datos[$campo]}'";
                }
            }

            if (!empty($cambios)) {
                TicketService::editar($ticketId, $datos);

                $logFolio = FolioService::obtener('log_tickets');
                $descripcionLog = "Ticket actualizado: " . implode('; ', $cambios);
                TicketService::agregarLog($ticketId, $logFolio, $descripcionLog);
            }

            return true;
        });
    }

    public static function editarEstado(int $ticketId, array $datos)
    {
        return DB::transaction(function () use ($ticketId, $datos) {
            $ticketActual = TicketService::obtener($ticketId, 'ticketId,status');
            if (!$ticketActual) {
                throw new \Exception("Ticket no existe");
            }

            if ($ticketActual->status === $datos['status']) {
                return false; // No hay cambio
            }

            TicketService::editarEstado($ticketId, $datos);

            $folio = FolioService::obtener('log_tickets');
            $descripcion = "Estado cambiado de '{$ticketActual->status}' a '{$datos['status']}'";
            TicketService::agregarLog($ticketId, $folio, $descripcion);

            return true;
        });
    }

    public static function editarPrioridad(int $ticketId, array $datos)
    {
        return DB::transaction(function () use ($ticketId, $datos) {
            $ticketActual = TicketService::obtener($ticketId, 'ticketId,prioridad');
            if (!$ticketActual) {
                throw new \Exception("Ticket no existe");
            }

            if ($ticketActual->prioridad === $datos['prioridad']) {
                return false;
            }

            TicketService::editarPrioridad($ticketId, $datos);

            $folio = FolioService::obtener('log_tickets');
            $descripcion = "Prioridad cambiada de '{$ticketActual->prioridad}' a '{$datos['prioridad']}'";
            TicketService::agregarLog($ticketId, $folio, $descripcion);

            return true;
        });
    }

    public static function editarAsignacion(int $ticketId, array $datos)
    {
        return DB::transaction(function () use ($ticketId, $datos) {
            $ticketActual = TicketService::obtener($ticketId,'ticketId,usuarioAsignadoId');
            if (!$ticketActual) {
                throw new \Exception("Ticket no existe");
            }

            if ($ticketActual->usuarioAsignadoId === $datos['usuario_asignado_id']) {
                return false;
            }

            TicketService::editarAsignacion($ticketId, $datos);

            $folio = FolioService::obtener('log_tickets');
            $descripcion = "Asignación cambiada de usuario ID '{$ticketActual->usuarioAsignadoId}' a '{$datos['usuario_asignado_id']}'";
            TicketService::agregarLog($ticketId, $folio, $descripcion);

            return true;
        });
    }


    public static function obtener($id)
    {
        $ticket = TicketService::obtener($id, 'ticketId,clienteId,cliente,proyectoId,proyecto,etiquetaId,etiqueta,usuarioAsignadoId,usuarioAsignado,serieFolio,titulo,descripcion,prioridad,status,registroFecha,actualizacionFecha');
        $ticketFeedback = TicketFeedbackService::listar(['ticket_id' => $id], 'usuario,folio,comentario,registroFecha', ['folio' => 'desc']);
        $ticketLogs = TicketService::obtenerLogs($id, 'folio,descripcion,registroFecha', ['folio' => 'desc']);
        return ['ticket' => $ticket, 'feedback' => $ticketFeedback, 'logs' => $ticketLogs];
    }
}

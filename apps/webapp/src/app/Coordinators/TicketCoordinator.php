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
        $tickets = TicketService::listar([], 'ticketId,cliente,proyecto,etiqueta,usuarioAsignado,folio,serieFolio,titulo,descripcion,prioridad,status,registroFecha');
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
    public static function actualizarProyecto(int $id, array $data)
    {
        $ticketActual = TicketService::obtener($id, 'titulo,descripcion,prioridad,status,usuarioAsignadoId');
        if (!$ticketActual) {
            throw new \Exception("El proyecto con ID {$id} no existe.");
        }

        return DB::transaction(function () use ($id, $data, $ticketActual) {
            $folio = FolioService::obtener('log_tickets');
            $nombreUsuarioAnterior = null;
            $nombreUsuarioNuevo = null;
            if (
                isset($data['usuario_asignado_id']) &&
                $data['usuario_asignado_id'] != $ticketActual->usuarioAsignadoId
            ) {
                $nombreUsuarioAnterior = UsuarioService::obtenerNombre($ticketActual->usuarioAsignadoId);
                $nombreUsuarioNuevo = UsuarioService::obtenerNombre($data['usuario_asignado_id']);
            }
            
            return TicketService::editar($id,$data,$nombreUsuarioAnterior,$nombreUsuarioNuevo,$folio);
        });
    }

    public static function editarEstado(int $ticketId, array $datos)
    {
        return DB::transaction(function () use ($ticketId, $datos) {
            $ticketActual = TicketService::obtener($ticketId);
            if (!$ticketActual) {
                throw new \Exception("Ticket no existe");
            }

            if ($ticketActual->status === $datos['status']) {
                return false;
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
            $ticketActual = TicketService::obtener($ticketId);
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
            
            $nombreUsuarioAnterior = UsuarioService::obtenerNombre($ticketActual->usuarioAsignadoId);
            $nombreUsuarioNuevo = UsuarioService::obtenerNombre($datos['usuario_asignado_id']);
    
            TicketService::editarAsignacion($ticketId, $datos);
    
            $folio = FolioService::obtener('log_tickets');
            $descripcion = "Asignación cambiada de '{$nombreUsuarioAnterior}' a '{$nombreUsuarioNuevo}'";
    
            TicketService::agregarLog($ticketId, $folio, $descripcion);
    
            return true;
        });
    }
    

    public static function obtener($id)
    {
        $ticket = TicketService::obtener($id, 'ticketId,clienteId,proyectoId,etiquetaId,usuarioAsignadoId,titulo,descripcion,prioridad,status');
        $ticketFeedback = TicketFeedbackService::listar(['ticket_id' => $id], 'ticketFeedbackId,ticketId,usuario,folio,comentario,registroFecha', ['folio' => 'asc']);
        return ['ticket' => $ticket, 'feedback' => $ticketFeedback];
    }
}

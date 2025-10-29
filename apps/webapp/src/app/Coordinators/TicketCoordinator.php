<?php

namespace App\Coordinators;

use App\Consts\StatusConsts;
use App\Services\EtiquetaService;
use App\Services\FolioService;
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
        return ['tickets' => $tickets, 'etiquetas'=> $etiquetas, 'usuarios' => $usuarios];
    }
    
    public static function agregar($datos)
    {
        return DB::transaction(function () use($datos) {
            $folio = FolioService::obtener('ticket');
            $datos['folio'] = $folio;

            if (!TicketService::agregar($datos)) {
                throw new Exception('No se pudo crear el ticket');
            }

            return true;
        });
    }

    public static function obtener($id) {
        $ticket = TicketService::obtener($id, 'ticketId,clienteId,proyectoId,etiquetaId,usuarioAsignadoId,titulo,descripcion,prioridad,status');
        $ticketFeedback = TicketFeedbackService::listar(['ticket_id' => $id], 'ticketFeedbackId,ticketId,usuario,folio,comentario,registroFecha', ['folio' => 'asc']);
        return ['ticket' => $ticket, 'feedback' => $ticketFeedback];
    }
}

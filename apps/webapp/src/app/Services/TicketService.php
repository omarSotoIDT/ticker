<?php

namespace App\Services;

use App\BO\TicketBO;
use App\RepoAction\TicketRepoAction;
use App\RepoData\TicketRepoData;

class TicketService
{
    public static function listar($filtros = [], $columnas = '', $orden = [], $limit = null, $offset = null)
    {
        $tickets = TicketRepoData::listar($filtros, $columnas, $orden, $limit, $offset);
        return $tickets;
    }

    public static function obtener($id, $columnas = '')
    {
        return TicketRepoData::obtener($id, $columnas);
    }

    public static function agregar($datos)
    {
        $insertTicket = TicketBO::armarInsert($datos);
        return TicketRepoAction::crear($insertTicket);
    }

    public static function editar($id,$data) {
        $updateData = TicketBO::armarUpdate($data);
        $resultado = TicketRepoAction::actualizar($id, $updateData);
    
        return $resultado;
    }
    

    public static function editarEstado($id, $datos)
    {
        $updateTicket = TicketBO::armarUpdateEstado($datos);
        return TicketRepoAction::actualizar($id, $updateTicket);
    }

    public static function editarPrioridad($id, $datos)
    {
        $updateTicket = TicketBO::armarUpdatePrioridad($datos);
        return TicketRepoAction::actualizar($id, $updateTicket);
    }

    public static function editarAsignacion($id, $datos)
    {
        $updateTicket = TicketBO::armarUpdateAsignacion($datos);
        return TicketRepoAction::actualizar($id, $updateTicket);
    }

    public static function agregarLog(int $ticketId, int $folio, string $descripcion)
    {
        $insertLog = TicketBO::armarInsertLog([
            'ticket_id' => $ticketId,
            'folio' => $folio,
            'descripcion' => $descripcion,
        ]);
        return TicketRepoAction::crearLog($insertLog);
    }

    public static function obtenerLogs($id, $columnas = '', $orden = []) {
        $ticketLogs = TicketRepoData::obtenerLogs($id, $columnas, $orden);
        return $ticketLogs;
    }
}

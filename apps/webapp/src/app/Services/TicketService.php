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

    public static function agregar($datos)
    {
        $insertTicket = TicketBO::armarInsert($datos);
        return TicketRepoAction::crear($insertTicket);
    }

    public static function editar($id, $datos)
    {
        $updateTicket = TicketBO::armarUpdate($datos);
        return TicketRepoAction::actualizar($id, $updateTicket);
    }
}

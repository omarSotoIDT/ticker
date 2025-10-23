<?php

namespace App\Services;

use App\BO\TicketBO;
use App\RepoAction\TicketRepoAction;
use App\RepoData\TicketRepoData;

class TicketService
{
    public static function cargarGestor()
    {
        $tickets = self::listar([], 'ticketId,cliente,proyecto,etiqueta,usuarioAsignado,folio,titulo,descripcion,prioridad,status');
        return ['tickets' => $tickets];
    }
    public static function listar($filtros = [], $columnas = '', $orden = [], $limit = null, $offset = null)
    {
        $tickets = TicketRepoData::listar($filtros, $columnas, $orden, $limit, $offset);
        return $tickets;
    }

    public static function agregar($datos)
    {
        $insertUsuario = TicketBO::armarInsert($datos);
        return TicketRepoAction::crear($insertUsuario);
    }

    public static function editar($id, $datos)
    {
        $updateUsuario = TicketBO::armarUpdate($datos);
        return TicketRepoAction::actualizar($id, $updateUsuario);
    }
}

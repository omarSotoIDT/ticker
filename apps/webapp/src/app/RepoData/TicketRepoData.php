<?php

namespace App\RepoData;

use App\RH\TicketLogRH;
use App\RH\TicketRH;
use Illuminate\Support\Facades\DB;

class TicketRepoData
{
    public static function listar($filtros, $columnas, $orden, $limit, $offset)
    {
        $query = DB::table('tickets as t');
        $query->leftJoin('clientes as c', 't.cliente_id', '=', 'c.cliente_id');
        $query->leftJoin('proyectos as p', 't.proyecto_id', '=', 'p.proyecto_id');
        $query->leftJoin('etiquetas as e', 't.etiqueta_id', '=', 'e.etiqueta_id');
        $query->leftJoin('sys_usuarios as su', 't.usuario_asignado_id', '=', 'su.usuario_id');

        TicketRH::agregarColumnas($query, $columnas);
        TicketRH::agregarFiltros($query, $filtros);
        TicketRH::agregarOrden($query, $orden);

        if (isset($limit)) {
            $query->limit($limit);
        }
        if (isset($offset)) {
            $query->offset($offset);
        }
        return $query->get()->toArray();
    }

    public static function obtener($id, $columnas)
    {
        $query = DB::table('tickets as t');
        $query->leftJoin('clientes as c', 't.cliente_id', '=', 'c.cliente_id');
        $query->leftJoin('proyectos as p', 't.proyecto_id', '=', 'p.proyecto_id');
        $query->leftJoin('etiquetas as e', 't.etiqueta_id', '=', 'e.etiqueta_id');
        $query->leftJoin('sys_usuarios as su', 't.usuario_asignado_id', '=', 'su.usuario_id');

        TicketRH::agregarColumnas($query, $columnas);

        if (isset($id)) {
            $query->where('t.ticket_id', $id);
        }

        return $query->first();
    }

    public static function obtenerLogs($ticket_id, $columnas, $orden)
    {
        $query = DB::table('log_tickets as lt')
            ->leftJoin('sys_usuarios as su', 'lt.usuario_id', '=', 'su.usuario_id');

        TicketLogRH::agregarColumnas($query, $columnas);
        TicketLogRH::agregarOrden($query, $orden);

        $query->where('lt.ticket_id', $ticket_id);

        return $query->get()->toArray();
    }
}

<?php

namespace App\RepoData;

use App\RH\TicketFeedbackRH;
use Illuminate\Support\Facades\DB;

class TicketFeedbackRepoData
{
    public static function listar($filtros, $columnas, $orden, $limit, $offset) {
        $query = DB::table('tickets_feedback as tf');
        $query->leftJoin('tickets as t', 'tf.ticket_id', '=', 't.ticket_id');
        $query->leftJoin('sys_usuarios as su', 'tf.usuario_id', '=', 'su.usuario_id');


        TicketFeedbackRH::agregarColumnas($query, $columnas);
        TicketFeedbackRH::agregarFiltros($query, $filtros);
        TicketFeedbackRH::agregarOrden($query, $orden);

        if (isset($limit)) {
            $query->limit($limit);
        }
        if (isset($offset)) {
            $query->offset($offset);
        }
        return $query->get()->toArray();
    }
}

<?php

namespace App\Services;

use App\BO\TicketFeedbackBO;
use App\RepoAction\TicketFeedbackRepoAction;
use App\RepoData\TicketFeedbackRepoData;

class TicketFeedbackService
{
    public static function listar($filtros = [], $columnas = '', $orden = [], $limit = null, $offset = null)
    {
        $ticketsFeedback = TicketFeedbackRepoData::listar($filtros, $columnas, $orden, $limit, $offset);
        return $ticketsFeedback;
    }

    public static function agregar($datos)
    {
        $insertTicketFeedback = TicketFeedbackBO::armarInsert($datos);
        return TicketFeedbackRepoAction::crear($insertTicketFeedback);
    }
}

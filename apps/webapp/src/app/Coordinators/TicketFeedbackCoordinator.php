<?php

namespace App\Coordinators;

use App\Services\FolioService;
use App\Services\TicketFeedbackService;
use Exception;
use Illuminate\Support\Facades\DB;

class TicketFeedbackCoordinator
{
    public static function agregar($ticket_id, $datos)
    {
        $datos['ticket_id'] = $ticket_id;
        
        return DB::transaction(function () use ($datos) {
            $folio = FolioService::obtener('ticketFeedback');
            $datos['folio'] = $folio;

            if (!TicketFeedbackService::agregar($datos)) {
                throw new Exception('No se pudo agregar feedback');
            }

            return true;
        });
    }
}

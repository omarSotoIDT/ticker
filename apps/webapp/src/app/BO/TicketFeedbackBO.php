<?php

namespace App\BO;

use Illuminate\Support\Facades\Auth;

class TicketFeedbackBO
{
    public static function armarInsert($datos) {
        $ticketFeedback = [
            'ticket_id' => $datos['ticket_id'],
            'folio' => $datos['folio'],
            'usuario_id' => Auth::id(),
            'comentario' => $datos['comentario'],
            'registro_fecha' => now()
        ];

        return $ticketFeedback;
    }
}

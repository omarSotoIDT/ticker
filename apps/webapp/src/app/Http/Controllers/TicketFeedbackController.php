<?php

namespace App\Http\Controllers;

use App\Coordinators\TicketFeedbackCoordinator;
use App\Services\TicketFeedbackService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Throwable;

class TicketFeedbackController extends Controller
{
    public function listarRest($ticket_id)
    {
        try {
            $ticketsFeedback = TicketFeedbackService::listar(['ticketId' => $ticket_id], 'ticketFeedbackId,ticketId,usuario,folio,comentario,registroFecha', ['folio' => 'asc']);
            return Response::json($ticketsFeedback, 200);
        } catch (Throwable $error) {
            Log::error("Ocurrio un error al listar el feedback " . $error);
            return Response::json(['error' => 'Ocurrio un error al listar el feedback'], 500);
        }
    }
    public function agregarRest(Request $request, $ticket_id)
    {
        try {
            $datos = $request->validate([
                'comentario' => 'string|max:500|required'
            ]);
            if (TicketFeedbackCoordinator::agregar($ticket_id, $datos)) {
                return Response::json(null, 201);
            }
        } catch (ValidationException $e) {
            return Response::json(['errors'  => $e->errors()], 422);
        } catch (Throwable $error) {
            Log::error("Ocurrio un error al agregar feedback " . $error);
            return Response::json(['error' => 'Ocurrio un error al agregar feedback'], 500);
        }
    }
}

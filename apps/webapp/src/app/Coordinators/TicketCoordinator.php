<?php

namespace App\Coordinators;

use App\Services\FolioService;
use App\Services\TicketService;
use Exception;
use Illuminate\Support\Facades\DB;

class TicketCoordinator
{
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
}

<?php

namespace App\BO;

use App\Consts\TicketConsts;
use Illuminate\Support\Facades\Auth;

class TicketBO
{
    public static function armarInsert($datos)
    {
        $ticket = [
            'cliente_id' => $datos['cliente_id'],
            'proyecto_id' => $datos['proyecto_id'],
            'etiqueta_id' => $datos['etiqueta_id'],
            'usuario_asignado_id' => $datos['usuario_asignado_id'],
            'folio' => $datos['folio'],
            'serie_folio' => TicketConsts::SERIE . $datos['folio'],
            'titulo' => $datos['titulo'],
            'descripcion' => $datos['descripcion'],
            'prioridad' => $datos['prioridad'],
            'status' => TicketConsts::ABIERTO,
            'registro_autor_id' => Auth::id(),
            'registro_fecha' => now()
        ];

        return $ticket;
    }

    public static function armarUpdate($datos)
    {
        $ticket = [
            'cliente_id' => $datos['cliente_id'],
            'proyecto_id' => $datos['proyecto_id'],
            'etiqueta_id' => $datos['etiqueta_id'],
            'usuario_asignado_id' => $datos['usuario_asignado_id'],
            'titulo' => $datos['titulo'],
            'descripcion' => $datos['descripcion'],
            'prioridad' => $datos['prioridad'],
            'status' => $datos['status'],
            'actualizacion_autor_id' => Auth::id(),
            'actualizacion_fecha' => now()
        ];

        return $ticket;
    }

    public static function armarUpdateEstado($datos)
    {
        $ticket = [
            'status' => $datos['status'],
            'actualizacion_autor_id' => Auth::id(),
            'actualizacion_fecha' => now()
        ];

        return $ticket;
    }

    public static function armarUpdatePrioridad($datos)
    {
        $ticket = [
            'prioridad' => $datos['prioridad'],
            'actualizacion_autor_id' => Auth::id(),
            'actualizacion_fecha' => now()
        ];

        return $ticket;
    }

    public static function armarUpdateAsignacion($datos)
    {
        $ticket = [
            'usuario_asignado_id' => $datos['usuario_asignado_id'],
            'actualizacion_autor_id' => Auth::id(),
            'actualizacion_fecha' => now()
        ];

        return $ticket;
    }

    public static function armarInsertLog(array $datos): array
    {
        return [
            'ticket_id' => $datos['ticket_id'],
            'usuario_id' => Auth::id(),
            'folio' => $datos['folio'],
            'descripcion' => $datos['descripcion'],
            'registro_fecha' => now()
        ];
    }
}

<?php

namespace App\BO;

class TicketBO
{
    public static function armarInsert($datos)
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
        ];

        return $ticket;
    }

    public static function armarUpdate($datos)
    {
        $ticket = [
            'cliente_id' => $datos['cliente'],
            'proyecto_id' => $datos['proyecto'],
            'etiqueta_id' => $datos['etiqueta'],
            'usuario_asignado_id' => $datos['usuario_asignado'],
            'titulo' => $datos['titulo'],
            'descripcion' => $datos['descripcion'],
            'prioridad' => $datos['prioridad'],
            'status' => $datos['status'],
        ];

        return $ticket;
    }
}

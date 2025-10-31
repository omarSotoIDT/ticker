<?php

namespace App\RH;

class TicketFeedbackRH
{
    private static $columnasDisponibles = [
        'ticketFeedbackId' => 'tf.ticket_feedback_id',
        'ticketId' => 'tf.ticket_id',
        'ticket' => 't.titulo',
        'usuarioId' => 'tf.usuario_id',
        'usuario' => 'su.usuario',
        'folio' => 'tf.folio',
        'comentario' => 'tf.comentario',
        'registroFecha' => 'tf.registro_fecha',
    ];

    public static function agregarColumnas(&$query, $columnasSeleccionadas)
    {
        if (empty($columnasSeleccionadas)) {
            $query->select('tf.ticket_feedback_id');
            return;
        }

        $arregloColumnas = array_map('trim', explode(',', $columnasSeleccionadas));
        $columnas = [];

        foreach ($arregloColumnas as $col) {
            if (isset(self::$columnasDisponibles[$col])) {
                $columnas[] = self::$columnasDisponibles[$col] . ' AS ' . $col;
            }
        }

        $query->select($columnas);
    }

    public static function agregarFiltros(&$query, $filtros)
    {
        if (!empty($filtros)) {
            if (isset($filtros['ticket_id'])) {
                $query->where('tf.ticket_id', $filtros['ticket_id']);
            };
            if (isset($filtros['usuario_id'])) {
                $query->where('tf.usuario_id', $filtros['usuario_id']);
            };
        }
    }

    public static function agregarOrden(&$query, $orden)
    {
        if (!empty($orden)) {
            foreach ($orden as $columna => $direccion) {
                $query->orderBy($columna, $direccion);
            }
        }
    }
}

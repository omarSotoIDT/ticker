<?php

namespace App\RH;

class TicketLogRH
{
    private static $columnasDisponibles = [
        'logTicketId' => 'lt.log_ticket_id',
        'ticketId' => 'lt.ticket_id',
        'usuarioId' => 'lt.usuario_id',
        "usuario"   => 'su.usuario',
        'folio' => 'lt.folio',
        'descripcion' => 'lt.descripcion',
        'registroFecha' => 'lt.registro_fecha'
    ];

    public static function agregarColumnas(&$query, $columnasSeleccionadas)
    {
        if (empty($columnasSeleccionadas)) {
            $query->select('lt.log_ticket_id');
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
                $query->where('lt.ticket_id', $filtros['ticket_id']);
            };
            if (isset($filtros['usuario_id'])) {
                $query->where('lt.usuario_id', $filtros['usuario_id']);
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

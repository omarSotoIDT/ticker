<?php

namespace App\RH;

class TicketRH
{
    private static $columnasDisponibles = [
        'ticketId' => 't.ticket_id',
        'clienteId' => 'c.cliente_id',
        'cliente' => 'c.nombre',
        'proyectoId' => 'p.proyecto_id',
        'proyecto' => 'p.nombre',
        'etiquetaId' => 'e.etiqueta_id',
        'etiqueta' => 'e.titulo',
        'usuarioAsignadoId' => 'su.usuario_id',
        'usuarioAsignado' => 'su.usuario',
        'folio' => 't.folio',
        'serieFolio' => 't.serie_folio',
        'titulo' => 't.titulo',
        'descripcion' => 't.descripcion',
        'prioridad' => 't.prioridad',
        'status' => 't.status',
        'registroAutorId' => 't.registro_autor_id',
        'registroFecha' => 't.registro_fecha',
        'actualizacionAutorId' => 't.actualizacion_autor_id',
        'actualizacionFecha' => 't.actualizacion_fecha',
    ];

    public static function agregarColumnas(&$query, $columnasSeleccionadas)
    {
        if (empty($columnasSeleccionadas)) {
            $query->select('t.ticket_id');
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
            if (isset($filtros['titulo'])) {
                $query->where('t.titulo', 'like', '%' . $filtros['titulo'] . '%');
            };
            if (isset($filtros['prioridad'])) {
                $query->where('t.prioridad', $filtros['prioridad']);
            };
            if (isset($filtros['status'])) {
                $query->where('t.status', $filtros['status']);
            };
            if (isset($filtros['cliente_id'])) {
                $query->where('c.cliente_id', $filtros['cliente_id']);
            };
            if (isset($filtros['proyecto_id'])) {
                $query->where('p.proyecto_id', $filtros['proyecto_id']);
            };
            if (isset($filtros['etiqueta_id'])) {
                $query->where('e.etiqueta_id', $filtros['etiqueta_id']);
            };
            if (isset($filtros['usuario_asignado_id'])) {
                $query->where('su.usuario_id', $filtros['usuario_asignado_id']);
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

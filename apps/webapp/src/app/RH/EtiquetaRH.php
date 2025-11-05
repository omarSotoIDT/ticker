<?php

namespace App\RH;

class EtiquetaRH
{
    private static $columnasDisponibles = [
        'etiquetaId' => 'e.etiqueta_id',
        'titulo' => 'e.titulo',
        'descripcion' => 'e.descripcion',
        'status' => 'e.status'
    ];

    public static function agregarColumnas(&$query, $columnasSeleccionadas) {
        if (empty($columnasSeleccionadas)) {
            $query->select('e.etiqueta_id');
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
                $query->where('e.titulo', $filtros['titulo']);
            };
            if (isset($filtros['status'])) {
                $query->where('e.status', $filtros['status']);
            };
            if (isset($filtros['etiquetaId'])) {
                $query->where('e.etiqueta_id', $filtros['etiquetaId']);
            }
            
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

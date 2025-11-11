<?php

namespace App\RH;

use Illuminate\Support\Facades\DB;

class UsuarioRH
{
    private static $columnasDisponibles = [
        'usuarioId' => 'su.usuario_id',
        'usuario' => 'su.usuario',
        'email' => 'su.email',
        'acceso' => 'su.ultimo_acceso_fecha',
        'status' => 'su.status',
        'superUsuario' => 'su.super_usuario',
        'motivoEliminacion' => 'su.motivo_eliminacion',
        'registroAutorId' => 'su.registro_autor_id',
        'registroFecha' =>'su.registro_fecha',
        'actualizacionAutorId' => 'su.actualizacion_autor_id',
        'actualizacionFecha' => 'su.actualizacion_fecha',
        'idPerfiles' => 'sp.perfil_id',
        'nombrePerfiles' => 'sp.nombre',
    ];    

    public static function agregarColumnas(&$query, $columnasSeleccionadas)
    {   
        if (empty($columnasSeleccionadas)) {
            $query->select('su.usuario_id');
            return;
        }

        $arregloColumnas = array_map('trim', explode(',', $columnasSeleccionadas));
        $columnas = [];
        $agrupables = [];

        foreach ($arregloColumnas as $col) {
            if (isset(self::$columnasDisponibles[$col])) {
                $columna = self::$columnasDisponibles[$col];

                if (str_starts_with($columna, 'sp.')) {
                    $columnas[] = DB::raw("GROUP_CONCAT(DISTINCT {$columna}) AS {$col}");
                } else {
                    $columnas[] = "{$columna} AS {$col}";
                    $agrupables[] = $columna;
                }
            }
        }

        $query->select($columnas);
        if(!empty($agrupables)) $query->groupBy($agrupables);
    }

    public static function agregarFiltros(&$query, $filtros){
        if(!empty($filtros)){
            if (isset($filtros['status'])) {
                $query->where('su.status', $filtros['status']);
            };
            if (isset($filtros['usuario'])) {
                $query->where('su.usuario', 'like' ,'%' . $filtros['usuario'] . '%');
            };

            if (isset($filtros['email'])) {
                $query->where('su.email', $filtros['email']);
            };
        }
    }

    public static function agregarOrden(&$query, $orden){
        if(!empty($orden)){
            foreach($orden as $columna => $direccion){
                $query->orderBy($columna, $direccion);
            }
        }
    }
}

<?php

namespace App\RH;

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
        'perfilId' => 'sp.perfil_id',
        'nombrePerfil' => 'sp.nombre',
    ];    

    public static function agregarColumnas(&$query, $columnasSeleccionadas)
    {   
        if (empty($columnasSeleccionadas)) {
            $query->select('su.usuario_id');
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

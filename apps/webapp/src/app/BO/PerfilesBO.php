<?php

namespace App\BO;

class PerfilesBO
{
    public static function adaptarCreacion(array $datos)
    {
        return [
            'clave' => $datos['clave'],
            'nombre' => $datos['nombre'],
            'descripcion' => $datos['descripcion'],
            'status' => $datos['status'],
            'super_usuario' => $datos['super_usuario'],
            'registro_autor_id' => $datos['registro_autor_id'],
            'registro_fecha' => $datos['registro_fecha']
        ];
    }

    public static function adaptarActualizacion(array $datos)
    {
        return [
            'clave' => $datos['clave'],
            'nombre' => $datos['nombre'],
            'descripcion' => $datos['descripcion'],
            'status' => $datos['status'],
            'super_usuario' => $datos['super_usuario'],
            'actualizacion_autor_id' => $datos['actualizacion_autor_id'],
            'actualizacion_fecha' => $datos['actualizacion_fecha']
        ];
    }
}
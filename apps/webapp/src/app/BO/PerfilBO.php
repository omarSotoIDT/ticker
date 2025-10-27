<?php

namespace App\BO;

use Illuminate\Support\Facades\Auth;

class PerfilBO
{
    public static function armarInsert(array $datos)
    {
        return [
            'clave' => $datos['clave'],
            'nombre' => $datos['nombre'],
            'descripcion' => $datos['descripcion'],
            'status' => $datos['status'],
            'super_usuario' => $datos['super_usuario'],
            'registro_autor_id' => Auth::id(),
            'registro_fecha' => now(),
            'permisos' => $datos['permisos'] ?? []
        ];
    }

    public static function armarUpdate(array $datos)
    {
        return [
            'clave' => $datos['clave'],
            'nombre' => $datos['nombre'],
            'descripcion' => $datos['descripcion'],
            'status' => $datos['status'],
            'super_usuario' => $datos['super_usuario'],
            'actualizacion_autor_id' => Auth::id(),
            'actualizacion_fecha' => now(),
            'permisos' => $datos['permisos'] ?? []
        ];
    }
}
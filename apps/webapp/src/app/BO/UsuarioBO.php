<?php

namespace App\BO;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UsuarioBO
{
    public static function armarInsert($datos) {
        $usuario = [
            'usuario' => $datos['nombre'],
            'email' => $datos['email'],
            'password' => Hash::make($datos['contrasena']),
            'status' => 'ACTIVO',
            'registro_autor_id' => Auth::id() ?? 1,
            'registro_fecha' => now()
        ];

        return $usuario;
    }

    public static function armarUpdate($datos) {
        $usuario = [
            'usuario' => $datos['nombre'],
            'email' => $datos['email'],
            'password' => Hash::make($datos['contrasena']),
            'actualizacion_autor_id' => Auth::id(),
            'actualizacion_fecha' => now()
        ];

        return $usuario;
    }
    
    public static function armarDelete($datos) {
        $usuario = [
            'status' => 'ELIMINADO',
            'motivo_eliminacion' => $datos['motivo'],
            'actualizacion_autor_id' => Auth::id(),
            'actualizacion_fecha' => now()
        ];

        return $usuario;
    }

    public static function armarActivar() {
        $usuario = [
            'status' => 'ACTIVO',
            'motivo_eliminacion' => null,
            'actualizacion_autor_id' => Auth::id(),
            'actualizacion_fecha' => now()
        ];

        return $usuario;
    }
}

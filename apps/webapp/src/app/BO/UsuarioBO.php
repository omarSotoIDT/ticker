<?php

namespace App\BO;

use App\Consts\StatusConsts;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UsuarioBO
{
    public static function armarInsert($datos) {
        $usuario = [
            'usuario' => $datos['nombre'],
            'email' => $datos['email'],
            'password' => Hash::make($datos['password']),
            'status' => StatusConsts::ACTIVO,
            'registro_autor_id' => Auth::id(),
            'registro_fecha' => now()
        ];

        return $usuario;
    }

    public static function armarPerfilesInsert($usuario_id, $idPerfiles) {
        $usuarioPerfiles = array_map( fn($perfil_id) => [
            'perfil_id' => $perfil_id,
            'usuario_id' => $usuario_id,
            'registro_autor_id' => Auth::id(),
            'registro_fecha' => now()
        ], $idPerfiles);
        
        return $usuarioPerfiles;
    }

    public static function armarUpdate($datos) {
        $usuario = [
            'usuario' => $datos['nombre'],
            'email' => $datos['email'],
            'actualizacion_autor_id' => Auth::id(),
            'actualizacion_fecha' => now()
        ];

        if (isset($datos['password']) && $datos['password'] !== '') {
            $usuario['password'] = Hash::make($datos['password']);
        }

        return $usuario;
    }
    
    public static function armarDelete($datos) {
        $usuario = [
            'status' => StatusConsts::ELIMINADO,
            'motivo_eliminacion' => $datos['motivo'],
            'actualizacion_autor_id' => Auth::id(),
            'actualizacion_fecha' => now()
        ];

        return $usuario;
    }

    public static function armarActivar() {
        $usuario = [
            'status' => StatusConsts::ACTIVO,
            'motivo_eliminacion' => null,
            'actualizacion_autor_id' => Auth::id(),
            'actualizacion_fecha' => now()
        ];

        return $usuario;
    }
}

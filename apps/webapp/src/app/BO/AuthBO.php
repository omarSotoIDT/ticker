<?php

namespace App\BO;

class AuthBO
{
    public static function armarCredenciales($datos) {
        $credenciales = [
            'email' => $datos['email'],
            'password' => $datos['contrasena']
        ];
        
        return $credenciales;
    }
}

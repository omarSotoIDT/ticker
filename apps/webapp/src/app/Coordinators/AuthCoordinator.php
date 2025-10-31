<?php

namespace App\Coordinators;

use App\Services\AuthService;
use App\Services\UsuarioService;

class AuthCoordinator
{
    public static function iniciarSesion($datos) {
        if(AuthService::iniciarSesion($datos)){
            return UsuarioService::marcarAcceso(auth()->id());
        }
        return false;
    }
}

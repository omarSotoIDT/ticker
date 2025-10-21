<?php

namespace App\Coordinators;

use App\Services\AuthService;
use App\Services\UsuarioService;
use Illuminate\Support\Facades\Auth;

class AuthCoordinator
{
    public static function iniciarSesion($datos) {
        if(AuthService::iniciarSesion($datos)) {
            return true;
        }
        return false;
    }
}

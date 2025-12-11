<?php

namespace App\Coordinators;

use App\Services\AuthService;
use App\Services\UsuarioService;

class AuthCoordinator
{
    public static function iniciarSesion($datos) {
        if(AuthService::iniciarSesion($datos)){
            if (UsuarioService::verificarStatusUsuario(auth()->id())) {
                UsuarioService::marcarAcceso(auth()->id());
                return true;
            }
            AuthService::cerrarSesion();
            return 'eliminado';
        }
        return false;
    }
}

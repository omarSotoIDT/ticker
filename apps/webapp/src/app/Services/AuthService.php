<?php

namespace App\Services;

use App\BO\AuthBO;
use Illuminate\Support\Facades\Auth;

class AuthService
{
    public static function iniciarSesion($datos)
    {
        $credenciales = AuthBO::armarCredenciales($datos);
        if (Auth::attempt($credenciales)) {
            return true;
        }
        return false;
    }

    public static function cerrarSesion() {
        Auth::logout();
    }
}

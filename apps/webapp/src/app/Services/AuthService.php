<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;

class AuthService
{
    public static function ingresar($credenciales) 
    {
        if (Auth::attempt($credenciales)){
            return true;
        }
        return false;
    }
}

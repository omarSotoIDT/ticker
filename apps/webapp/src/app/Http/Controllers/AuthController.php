<?php

namespace App\Http\Controllers;

use App\Coordinators\AuthCoordinator;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Throwable;

class AuthController extends Controller
{
    public function iniciarSesion(Request $request)
    {
        try {
            $datos = $request->validate([
                'email' => 'required|email',
                'contrasena' => 'required'
            ]);
            if (AuthCoordinator::iniciarSesion($datos)) return redirect()->route('dashboard');
            return redirect()->back()->with('error', 'Las credenciales ingresadas no son validas')->withInput();
        } catch (ValidationException $e) {
            throw $e;
        } catch (Throwable $error) {
            Log::error('Ha ocurrido un error al iniciar sesion' . $error);
            return redirect()->back()->with('error', 'Ocurrio un error al iniciar sesión')->withInput();
        }
    }

    public function cerrarSesion() {
        try {
            AuthService::cerrarSesion();
            return redirect()->route('login');
        } catch (Throwable $error) {
            Log::error("Ocurrio un error al cerrar sesión " . $error);
            return redirect()->back()->with('error', 'Ocurrio un error al cerrar sesión');
        }
    }
}

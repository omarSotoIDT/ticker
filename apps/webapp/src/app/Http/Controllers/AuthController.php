<?php

namespace App\Http\Controllers;

use App\BO\AuthBO;
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
            if (AuthService::ingresar($datos)) return redirect()->route('tickets.dashboard');
            return redirect()->back()->with('error', 'Las credenciales ingresadas no son validas')->withInput();
        } catch (ValidationException $e) {
            throw $e;
        } catch (Throwable $error) {
            Log::error('Ha ocurrido un error al iniciar sesion' . $error);
            return redirect()->back()->with('error', 'Ocurrio un error al iniciar sesión')->withInput();
        }
    }
}

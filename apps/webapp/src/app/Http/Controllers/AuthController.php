<?php

namespace App\Http\Controllers;

use App\Coordinators\AuthCoordinator;
use App\Services\AuthService;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
            
            $resultado = AuthCoordinator::iniciarSesion($datos);

            if ($resultado === true) {                
                $usuario = Auth::user();

                $sesionExistente = DB::table('sessions')->where('user_id', $usuario->usuario_id)->exists();

                if ($sesionExistente) {
                    Auth::logout(); 
                    return redirect()->back()->with('error', 'Ya existe una sesión activa para este usuario.')->withInput();
                }

                $request->session()->regenerate();
                return redirect()->route('dashboard');
            }
            
            $mensajeError = 'Las credenciales ingresadas no son validas';
            if ($resultado === 'eliminado') {
                $mensajeError = 'El usuario se encuentra eliminado y no puede iniciar sesión.';
            }

            return redirect()->back()->with('error', $mensajeError)->withInput();
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
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
<?php

namespace App\Http\Controllers;

use App\Coordinators\UsuarioCoordinator;
use App\Coordinators\PerfilCoordinator;
use App\Services\UsuarioService;
use App\Services\PerfilService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Throwable;

class UsuarioController extends Controller
{
    public function gestor()
    {
        try {
            $resultado = PerfilCoordinator::obtenerPerfiles([], false);
            $perfiles = $resultado['perfiles_sin_paginar'] ?? $resultado['perfiles'] ?? [];
            return view('usuarios.usuariosGestor', ['perfiles' => $perfiles]);
        } catch (Throwable $error) {
            Log::error("Ocurrio un error al mostrar el gestor " . $error);
            return redirect()->back()->with('error', 'Ocurrio un error al mostrar el gestor');
        }
    }

    public static function listarRest(Request $request) {
        try {
            $filtros = $request->only(['usuario']);
            $resultado = UsuarioCoordinator::obtenerUsuarios($filtros, true);

            return Response::json([
                'usuarios' => $resultado['usuarios'],
                'links' => $resultado['links'],
            ], 200);
        } catch (Throwable $error) {
            Log::error("Ocurrio un error al listar los usuarios " . $error);
            return Response::json(['error' => 'Ocurrio un error al listar los usuarios'], 500);
        }
    }

    public static function listarUsuarios(Request $request) {
        try {
            $usuarios = UsuarioCoordinator::listarUsuarios($request->only(['usuario']));
            return Response::json($usuarios, 200);
        } catch (Throwable $error) {
            Log::error("Ocurrio un error al listar los usuarios " . $error);
            return Response::json(['error' => 'Ocurrio un error al listar los usuarios'], 500);
        }
    }

    public function agregarRest(Request $request){
        try {
            $datos = $request->validate([
                'nombre' => 'string|required|max:70',
                'email' => 'email:filter|unique:sys_usuarios|required|max:80',
                'password' => 'string|required|max:50',
                'perfiles' => 'array',
            ]);
            $id = UsuarioCoordinator::agregar($datos);

            return Response::json($id, 201);
            
        } catch (ValidationException $e) {
            return Response::json(['errors' => $e->errors()], 422);
        } catch (Throwable $error) {
            Log::error("Ocurrio un error al agregar al usuario " . $error);
            return Response::json(['error' => 'Ocurrio un error al agregar al usuario'], 500);
        }
    }

    public static function editarRest(Request $request, $id) {
        try {
            $datos = $request->validate([
                'nombre' => 'string|max:70',
                'email' => 'email:filter|max:80|unique:sys_usuarios,email,' . $id . ',usuario_id',
                'password' => 'nullable|string|max:50',
                'perfiles' => 'array',
            ]);

            if (UsuarioService::editar($id, $datos) !== 0) {
                return Response::json(null, 204);
            }
        } catch (ValidationException $e) {
            return Response::json(['errors' => $e->errors()], 422);
        } catch (Throwable $error) {
            Log::error("Ocurrio un error al editar el usuario " . $error);
            return Response::json(['error' => 'Ocurrio un error al editar el usuario'], 500);
        }
    }

    public static function eliminarRest(Request $request, $id) {
        try {
            $datos = $request->validate([
                'motivo' => 'string|required'
            ]);

            if (UsuarioCoordinator::eliminar($id, $datos)) {
                return Response::json(null, 204);
            }
        } catch (ValidationException $e) {
            return Response::json(['errors' => $e->errors()], 422);
        } catch (Throwable $error) {
            Log::error("Ocurrio un error al eliminar el usuario " . $error);
            return Response::json(['mensaje' => $error->getMessage()], 409);
        }
    }

    public static function activarRest($id) {
        try {
            if (UsuarioService::activar($id)) {
                return Response::json(null, 204);
            }
        } catch (ValidationException $e) {
            return Response::json(['errors' => $e->errors()], 422);
        } catch (Throwable $error) {
            Log::error("Ocurrio un error al activar el usuario " . $error);
            return Response::json(['error' => 'Ocurrio un error al activar el usuario'], 500);
        }
    }
}

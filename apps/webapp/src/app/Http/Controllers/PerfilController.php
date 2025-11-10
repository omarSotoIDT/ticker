<?php

namespace App\Http\Controllers;

use App\Coordinators\PerfilCoordinator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;
use Throwable;

class PerfilController extends Controller
{
    public function gestor()
    {
        return view('perfiles.perfiles');
    }

    public static function listarRest(Request $request)
    {
        try {
            $filtros = $request->all();

            $resultado = PerfilCoordinator::obtenerPerfiles($filtros);

            return response()->json([
                'perfiles' => $resultado['perfiles'],
                'links' => $resultado['links'],
                'permisos' => $resultado['permisos'],
            ]);
        } catch (Throwable $error) {
            Log::error("Error al listar perfiles: " . $error->getMessage());
            return response()->json([
                'error' => 'Ocurrió un error al listar los perfiles'
            ], 500);
        }
    }

    public static function agregarRest(Request $request)
    {
        try {
            $datos = $request->validate([
                'clave' => 'string|required|max:100|',
                'nombre' => 'string|required|max:100',
                'descripcion' => 'string|required|max:200',
                'permisos' => 'array',
            ]);

            $perfil_id = PerfilCoordinator::crearPerfil($datos);

            return Response::json($perfil_id, 201);
        } catch (ValidationException $e) {
            return Response::json(['errors' => $e->errors()], 422);
        } catch (Throwable $error) {
            Log::error("Error al crear perfil: " . $error);
            return Response::json(['error' => 'Ocurrió un error al crear el perfil'], 500);
        }
    }

    public static function editarRest(Request $request, $perfil_id)
    {
        try {
            $datos = $request->validate([
                'clave' => 'string|required|max:100|',
                'nombre' => 'string|required|max:100',
                'descripcion' => 'string|required|max:200',
                'status' => 'in:ACTIVO,ELIMINADO',
                'permisos' => 'array',
            ]);

            PerfilCoordinator::actualizarPerfil($perfil_id, $datos);

            return Response::json(null, 204);
        } catch (ValidationException $e) {
            return Response::json(['errors' => $e->errors()], 422);
        } catch (Throwable $error) {
            Log::error("Error al editar perfil: " . $error);
            return Response::json(['error' => 'Ocurrió un error al actualizar el perfil'], 500);
        }
    }

    public static function eliminarRest(Request $request, $perfil_id)
{
    try {
        $validated = Validator::make(
            ['perfil_id' => $perfil_id],
            ['perfil_id' => 'required|integer|min:1']
        )->validate();

        PerfilCoordinator::eliminarPerfil($validated['perfil_id']);

        return Response::json(null, 204);

    } catch (ValidationException $e) {
        return Response::json(['errors' => $e->errors()], 422);

    } catch (Throwable $error) {
        Log::error("Error al eliminar perfil: " . $error);
        return Response::json(['error' => 'Ocurrió un error al eliminar el perfil'], 500);
    }
}
}

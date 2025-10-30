<?php

namespace App\Http\Controllers;

use App\Services\ProyectoService;
use App\RepoData\ProyectoRepoData;
use App\Coordinators\ProyectoCoordinator;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use Throwable;

class ProyectoController extends Controller
{

    private function handleException(Throwable $e, string $mensajeUsuario, ?string $contexto = null)
    {
        $contextoTexto = $contexto ? " ({$contexto})" : '';
        Log::error("Error en ProyectoController{$contextoTexto}: " . $e->getMessage(), [
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString(),
            'request' => request()->all(),
        ]);

        if (request()->wantsJson()) {
            return response()->json(['error' => $mensajeUsuario], 500);
        }
    }


    private function validarProyecto(Request $request, bool $esEditar = false): array
    {
        $reglas = [
            'cliente_id'  => 'required|integer|exists:clientes,cliente_id',
            'nombre'      => 'required|string|max:150',
            'descripcion' => 'required|string|max:250',
        ];

        if ($esEditar) {
            $reglas['proyecto_id'] = 'required|integer';
        }

        return $request->validate($reglas);
    }


    public function listarRest(Request $request)
    {
        try {
            $busqueda = $request->query('busqueda');
            $filtros = [];

            if (!empty($busqueda)) {
                $filtros['busqueda'] = $busqueda;
            }

            $proyectos = ProyectoRepoData::obtenerProyectos($filtros);
            $proyectos = $proyectos->map(function ($p) {
                $p->cliente = [
                    'cliente_id' => $p->cliente_id,
                    'nombre' => $p->cliente_nombre
                ];
                unset($p->cliente_nombre);
                return $p;
            });
            return response()->json(['data' => $proyectos], 200);
        } catch (Throwable $e) {
            return $this->handleException($e, 'Error al obtener la lista de proyectos', __FUNCTION__);
        }
    }

    public function registrarRest(Request $request)
    {
        try {
            $data = $this->validarProyecto($request, false);
            $usuarios = $request->input('usuarios', []);

            $proyectoId = ProyectoCoordinator::crearProyecto($data);

            if (!empty($usuarios)) {
                ProyectoCoordinator::actualizarAsignacionesUsuarios($proyectoId, $usuarios);
            }

            return response()->json([
                'mensaje' => 'Proyecto registrado correctamente.'
            ], 201);
        } catch (ValidationException $e) {
            return response()->json(['errores' => $e->errors()], 422);
        } catch (Throwable $e) {
            return $this->handleException($e, 'Error al registrar el proyecto', __FUNCTION__);
        }
    }

    public function actualizarRest(Request $request, $id)
    {
        try {
            $data = $this->validarProyecto($request, true);
            $usuarios = $request->input('usuarios', []);

            ProyectoCoordinator::actualizarProyecto($id, $data);

            if (!empty($usuarios)) {
                ProyectoCoordinator::actualizarAsignacionesUsuarios($id, $usuarios);
            }

            return response()->json([
                'mensaje' => 'Proyecto actualizado correctamente.'
            ], 200);
        } catch (ValidationException $e) {
            return response()->json(['errores' => $e->errors()], 422);
        } catch (Throwable $e) {
            return $this->handleException($e, 'Error al actualizar el proyecto', __FUNCTION__);
        }
    }

    public function eliminarRest(Request $request, $id)
    {
        try {
            $request->validate([
                'motivo_eliminacion' => 'required|string|max:250'
            ]);

            ProyectoCoordinator::eliminarProyecto($id, $request->motivo_eliminacion);

            return response()->json(['mensaje' => 'Proyecto eliminado correctamente.'], 200);
        } catch (ValidationException $e) {
            return response()->json(['errores' => $e->errors()], 422);
        } catch (Throwable $e) {
            return $this->handleException($e, 'Error al eliminar el proyecto', __FUNCTION__);
        }
    }

    public function cambiarStatus($id)
    {
        try {
            ProyectoCoordinator::cambiarStatus($id);

            return response()->json(['mensaje' => 'Estado del proyecto actualizado correctamente.'], 200);
        } catch (Throwable $e) {
            return $this->handleException($e, 'Error al actualizar el estado del proyecto', __FUNCTION__);
        }
    }

    public function logsRest($id)
    {
        try {
            $logs = ProyectoService::obtenerLogs($id);
            $usuarios = ProyectoRepoData::listarUsuariosAsignados($id);

            return response()->json([
                'logs' => $logs,
                'usuarios' => $usuarios
            ], 200);
        } catch (Throwable $e) {
            return $this->handleException($e, 'Error al obtener logs del proyecto', __FUNCTION__);
        }
    }

    public function gestor()
    {
        try {
            return view('proyectos.Proyectos');
        } catch (Throwable $e) {
            return $this->handleException($e, 'Error al cargar la vista de proyectos', __FUNCTION__);
        }
    }

    public function usuariosRest($id)
    {
        try {
            $usuarios = ProyectoService::listarUsuariosAsignados($id);
            return response()->json(['data' => $usuarios], 200);
        } catch (Throwable $e) {
            return $this->handleException($e, 'Error al obtener usuarios asignados', __FUNCTION__);
        }
    }
}

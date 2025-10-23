<?php

namespace App\Http\Controllers;

use App\Services\ProyectoService;
use App\RepoData\ProyectoRepoData;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use Throwable;

class ProyectoController extends Controller
{
    /**
     * Manejo centralizado de excepciones
     */
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

        return back()->withErrors([$mensajeUsuario]);
    }

    /**
     * Validación de datos del proyecto
     */
    private function validarProyecto(Request $request, bool $esEditar = false): array
    {
        $reglas = [
            'cliente_id'  => 'required|integer|exists:clientes,cliente_id',
            'nombre'      => 'required|string|max:150',
            'descripcion' => 'nullable|string|max:250',
            'status'      => 'required|in:ACTIVO,INACTIVO,ELIMINADO',
        ];

        if ($esEditar) {
            $reglas['proyecto_id'] = 'required|integer';
        }

        return $request->validate($reglas);
    }

    /**
     * Obtener lista de proyectos
     */
    public function listarRest(Request $request)
    {
        try {
            $busqueda = $request->query('busqueda');
            $filtros = [];

            if (!empty($busqueda)) {
                $filtros['busqueda'] = $busqueda;
            }

            $proyectos = ProyectoRepoData::obtenerProyectos($filtros);

            return response()->json(['data' => $proyectos], 200);
        } catch (Throwable $e) {
            return $this->handleException($e, 'Error al obtener la lista de proyectos', __FUNCTION__);
        }
    }

    /**
     * Registrar nuevo proyecto
     */
    public function registrarRest(Request $request)
    {
        try {
            $data = $this->validarProyecto($request, false);
            ProyectoService::registrarProyecto($data);

            return response()->json(['mensaje' => 'Proyecto registrado correctamente.'], 201);
        } 
        catch (ValidationException $e) {
            return response()->json(['errores' => $e->errors()], 422);
        }
        catch (Throwable $e) {
            return $this->handleException($e, 'Error al registrar el proyecto', __FUNCTION__);
        }
    }

    /**
     * Actualizar proyecto existente
     */
    public function actualizarRest(Request $request, $id)
    {
        try {
            $data = $this->validarProyecto($request, true);
            ProyectoService::actualizarProyecto($id, $data);

            return response()->json(['mensaje' => 'Proyecto actualizado correctamente.'], 200);
        } 
        catch (ValidationException $e) {
            return response()->json(['errores' => $e->errors()], 422);
        }
        catch (Throwable $e) {
            return $this->handleException($e, 'Error al actualizar el proyecto', __FUNCTION__);
        }
    }

    /**
     * Eliminar (marcar como eliminado) un proyecto
     */
    public function eliminarRest(Request $request, $id)
    {
        try {
            $request->validate([
                'motivo_eliminacion' => 'required|string|max:250'
            ]);

            ProyectoService::eliminarProyecto($id, $request->motivo_eliminacion);

            return response()->json(['mensaje' => 'Proyecto eliminado correctamente.'], 200);
        } 
        catch (ValidationException $e) {
            return response()->json(['errores' => $e->errors()], 422);
        }
        catch (Throwable $e) {
            return $this->handleException($e, 'Error al eliminar el proyecto', __FUNCTION__);
        }
    }

    /**
     * Activar o desactivar un proyecto
     */
    public function activarRest($id)
    {
        try {
            ProyectoService::activarProyecto($id);

            return response()->json(['mensaje' => 'Estado del proyecto actualizado correctamente.'], 200);
        } 
        catch (Throwable $e) {
            return $this->handleException($e, 'Error al actualizar el estado del proyecto', __FUNCTION__);
        }
    }

    /**
     * Obtener logs de un proyecto
     */
    public function logsRest($id)
    {
        try {
            $logs = ProyectoService::obtenerLogs($id);

            return response()->json(['data' => $logs], 200);
        } 
        catch (Throwable $e) {
            return $this->handleException($e, 'Error al obtener logs del proyecto', __FUNCTION__);
        }
    }

    /**
     * Vista del gestor de proyectos
     */
    public function gestor()
    {
        try {
            return view('proyectos.gestor_proyectos');
        } catch (Throwable $e) {
            return $this->handleException($e, 'Error al cargar la vista de proyectos', __FUNCTION__);
        }
    }
}

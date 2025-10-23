<?php

namespace App\Http\Controllers;

use App\Coordinators\TicketCoordinator;
use App\Services\TicketService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Throwable;

class TicketController extends Controller
{
    public static function gestor()
    {
        try {
            $datos = TicketCoordinator::cargarGestor();
            return view('tickets.ticketsGestor', $datos);
        } catch (Throwable $error) {
            Log::error("Ocurrio un error al mostrar el gestor " . $error);
            return redirect()->back()->with('error', 'Ocurrio un error al mostrar el gestor');
        }
    }
    public function listarRest(Request $request)
    {
        try {
            $usuarios = TicketService::listar();
            return Response::json($usuarios, 200);
        } catch (Throwable $error) {
            Log::error("Ocurrio un error al listar los tickets " . $error);
            return Response::json(['error' => 'Ocurrio un error al listar los tickets'], 500);
        }
    }

    public function agregarRest(Request $request)
    {
        try {
            $datos = $request->validate([
                'cliente_id' => 'integer|required',
                'proyecto_id' => 'integer|required',
                'etiqueta_id' => 'integer|required',
                'usuario_asignado_id' => 'integer|required',
                'titulo' => 'string|required|max:100',
                'descripcion' => 'string|required|max:250',
                'prioridad' => 'in:Baja,Alta,Media,Urgente|required',
                'status' => 'in:Abierto,En progreso,Atendido,Cerrado,Informacion requerida,Cancelado|required',
            ]);

            if (TicketCoordinator::agregar($datos)) {
                Response::json(null, 201);
            }
        } catch (ValidationException $e) {
            return Response::json(['errors'  => $e->errors()], 422);
        } catch (Throwable $error) {
            Log::error("Ocurrio un error al agregar el ticket " . $error);
            return Response::json(['error' => 'Ocurrio un error al agregar el ticket'], 500);
        }
    }

    public function editarRest(Request $request, $id)
    {
        try {
            $datos = $request->validate([
                'cliente_id' => 'integer',
                'proyecto_id' => 'integer',
                'etiqueta_id' => 'integer',
                'usuario_asignado_id' => 'integer',
                'titulo' => 'string|max:100',
                'descripcion' => 'string|required',
                'prioridad' => 'in:Baja,Alta,Media,Urgente',
                'status' => 'in:Abierto,En progreso,Atendido,Cerrado,Informacion requerida,Cancelado',
            ]);
            if (TicketService::editar($id, $datos)) {
                return Response::json(null, 204);
            }
        } catch (ValidationException $e) {
            return Response::json(['errors' => $e->errors()], 422);
        } catch (Throwable $error) {
            Log::error("Ocurrio un error al editar el ticket " . $error);
            return Response::json(['error' => 'Ocurrio un error al editar el ticket'], 500);
        }
    }

    public static function editarEstadoRest(Request $request, $id)
    {
        try {
            $datos = $request->validate([
                'status' => 'in:Abierto,En progreso,Atendido,Cerrado,Informacion requerida,Cancelado|required',
            ]);
            if (TicketService::editarEstado($id, $datos)) {
                return Response::json(null, 204);
            }
        } catch (ValidationException $e) {
            return Response::json(['errors'  => $e->errors()], 422);
        } catch (Throwable $error) {
            Log::error("Ocurrio un error al editar el estado " . $error);
            return Response::json(['error' => 'Ocurrio un error al editar el estado'], 500);
        }
    }

    public static function editarPrioridadRest(Request $request, $id)
    {
        try {
            $datos = $request->validate([
                'prioridad' => 'in:Baja,Alta,Media,Urgente|required'
            ]);
            if (TicketService::editarPrioridad($id, $datos)) {
                return Response::json(null, 204);
            }
        } catch (ValidationException $e) {
            return Response::json(['errors'  => $e->errors()], 422);
        } catch (Throwable $error) {
            Log::error("Ocurrio un error al editar la prioridad " . $error);
            return Response::json(['error' => 'Ocurrio un error al editar la prioridad'], 500);
        }
    }

    public static function editarAsignacionRest(Request $request, $id)
    {
        try {
            $datos = $request->validate([
                'usuario_asignado_id' => 'integer|required'
            ]);
            if (TicketService::editarAsignacion($id, $datos)) {
                return Response::json(null, 204);
            }
        } catch (ValidationException $e) {
            return Response::json(['errors'  => $e->errors()], 422);
        } catch (Throwable $error) {
            Log::error("Ocurrio un error al editar la asignación " . $error);
            return Response::json(['error' => 'Ocurrio un error al editar la asignación'], 500);
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Services\ClienteService;
use App\RepoData\ClienteRepoData;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use Throwable;

class ClienteController extends Controller
{
    private function handleException(Throwable $e, string $mensajeUsuario, ?string $contexto = null)
    {
        $contextoTexto = $contexto ? " ({$contexto})" : '';
        Log::error("Error en ClienteController{$contextoTexto}: " . $e->getMessage(), [
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

    private function validarCliente(Request $request, bool $esEditar = false): array
    {
        $reglas = [
            'nombre' => 'required|string|max:150',
            'descripcion' => 'nullable|string|max:250',
            'contacto' => 'nullable|string|max:150',
            'email' => 'nullable|email|max:150',
        ];

        if ($esEditar) {
            $reglas['cliente_id'] = 'required|integer';
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
    
            $clientes = ClienteRepoData::obtenerClientes($filtros);
    
            return response()->json(['data' => $clientes], 200);
    
        } catch (Throwable $e) {
            return $this->handleException($e, 'Error al obtener la lista de clientes', __FUNCTION__);
        }
    }
    
    

    public function registrarRest(Request $request)
    {
        try {
            $data = $this->validarCliente($request, false);
            ClienteService::registrarCliente($data);

            return response()->json([
                'mensaje' => 'Cliente registrado correctamente.'
            ], 201);
        } 
        catch (ValidationException $e) {
            return response()->json([
                'errores' => $e->errors()
            ], 422);
        }
        catch (Throwable $e) {
            return $this->handleException($e, 'Error al registrar el cliente', __FUNCTION__);
        }
    }

    public function actualizarRest(Request $request, $id)
    {
        try {
            $data = $this->validarCliente($request, true);
            ClienteService::actualizarCliente($id, $data);

            return response()->json([
                'mensaje' => 'Cliente actualizado correctamente.'
            ], 212);
        } 
        catch (ValidationException $e) {
            return response()->json([
                'errores' => $e->errors()
            ], 422);
        }
        catch (Throwable $e) {
            return $this->handleException($e, 'Error al actualizar el cliente', __FUNCTION__);
        }
    }

    public function eliminarRest(Request $request, $id)
    {
        try {
            $request->validate([
                'motivo_eliminacion' => 'required|string|max:250'
            ]);

            ClienteService::eliminarCliente($id, $request->motivo_eliminacion);

            return response()->json([
                'mensaje' => 'Cliente eliminado correctamente.'
            ], 200);
        } 
        catch (ValidationException $e) {
            return response()->json([
                'errores' => $e->errors()
            ], 422);
        }
        catch (Throwable $e) {
            return $this->handleException($e, 'Error al eliminar el cliente', __FUNCTION__);
        }
    }

    public function activarRest($id)
    {
        try {
            ClienteService::activaCliente($id);

            return response()->json([
                'mensaje' => 'Cliente activado correctamente.'
            ], 200);
        } 
        catch (Throwable $e) {
            return $this->handleException($e, 'Error al actualizar el estado del cliente', __FUNCTION__);
        }
    }

    public function gestor(){
        try{
            return view('clientes.Clientes');
        }catch(Throwable $e){
            return $this->handleException($e, 'Error al cargar la vista cliente', __FUNCTION__);
        }
        
    }
}

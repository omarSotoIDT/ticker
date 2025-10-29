<?php

namespace App\Coordinators;

use App\Services\ProyectoService;
use App\Services\ClienteService;
use Illuminate\Support\Facades\DB;
use App\Services\FolioService;
use Illuminate\Support\Facades\Auth;

class ProyectoCoordinator
{

    public static function crearProyecto(array $data)
    {
        $clienteId = $data['cliente_id'];

        $clientes = ClienteService::listarClientes(['cliente_id' => $clienteId]);
        if ($clientes->isEmpty()) {
            throw new \Exception("Cliente con ID {$clienteId} no existe o está eliminado.");
        }
        $clienteNombre = ClienteService::obtenerNombre($data['cliente_id']);
        $proyectoId = ProyectoService::registrarProyecto($data, $clienteNombre);

        return $proyectoId;
    }

    public static function actualizarProyecto(int $id, array $data)
    {
        $proyectoActual = ProyectoService::obtenerPorId($id);
        if (!$proyectoActual) {
            throw new \Exception("El proyecto con ID {$id} no existe.");
        }

        $nombreClienteAnterior = null;
        $nombreClienteNuevo = null;

        if (isset($data['cliente_id']) && $data['cliente_id'] != $proyectoActual->cliente_id) {
            $nombreClienteAnterior = ClienteService::obtenerNombre($proyectoActual->cliente_id);
            $nombreClienteNuevo = ClienteService::obtenerNombre($data['cliente_id']);
        }

        return ProyectoService::actualizarProyecto($id, $data, $nombreClienteAnterior, $nombreClienteNuevo);
    }

    public static function ClientesDisponibles(array $filtros = [])
    {
        return ClienteService::listarClientes($filtros);
    }

    public static function registrarLog(int $proyectoId, string $descripcion): void
    {
        DB::transaction(function () use ($proyectoId, $descripcion) {
            $folio = FolioService::obtener('log_proyectos');

            $datos = [
                'proyecto_id' => $proyectoId,
                'usuario_id'  => Auth::id(),
                'folio'       => $folio,
                'descripcion' => $descripcion,
            ];

            ProyectoService::agregarLog($datos);
        });
    }
}

<?php

namespace App\Coordinators;

use App\Services\ProyectoService;
use App\Services\TicketService;
use App\Consts\TicketConsts;
use App\Services\ClienteService;
use Illuminate\Support\Facades\DB;
use App\Services\FolioService;

class ProyectoCoordinator
{

    public static function crearProyecto(array $datos)
    {
        $clienteId = $datos['cliente_id'];

        $clientes = ClienteService::listarClientes(['cliente_id' => $clienteId]);
        if ($clientes->isEmpty()) {
            throw new \Exception("Cliente con ID {$clienteId} no existe o está eliminado.");
        }
        return DB::transaction(function () use ($datos) {
            $folio = FolioService::obtener('log_proyectos');

            $clienteNombre = ClienteService::obtenerNombre($datos['cliente_id']);

            $proyectoId = ProyectoService::registrarProyecto($datos, $clienteNombre, $folio);
            return $proyectoId;
        });
    }
    public static function actualizarProyecto(int $id, array $data)
    {
        $proyectoActual = ProyectoService::obtenerPorId($id);
        if (!$proyectoActual) {
            throw new \Exception("El proyecto con ID {$id} no existe.");
        }

        return DB::transaction(function () use ($id, $data, $proyectoActual) {
            $folio = FolioService::obtener('log_proyectos');

            $nombreClienteAnterior = null;
            $nombreClienteNuevo = null;

            if (isset($data['cliente_id']) && $data['cliente_id'] != $proyectoActual->cliente_id) {
                $nombreClienteAnterior = ClienteService::obtenerNombre($proyectoActual->cliente_id);
                $nombreClienteNuevo = ClienteService::obtenerNombre($data['cliente_id']);
            }

            return ProyectoService::actualizarProyecto(
                $id,
                $data,
                $nombreClienteAnterior,
                $nombreClienteNuevo,
                $folio
            );
        });
    }

    public static function eliminarProyecto(int $id, string $motivo)
    {
        $ticketsActivos = TicketService::listar(
            [
                'proyecto_id' => $id,
                'status_excluidos' => [TicketConsts::CERRADO, TicketConsts::CANCELADO]
            ],
            'ticketId' 
        );

        if (count($ticketsActivos) > 0) {
            throw new \Exception(
                "No se puede eliminar el proyecto porque tiene " . count($ticketsActivos) . " ticket(s) que no están cerrados o cancelados."
            );
        }
        return DB::transaction(function () use ($id, $motivo) {
            $folio = FolioService::obtener('log_proyectos');

            $proyecto = ProyectoService::obtenerPorId($id);
            if (!$proyecto) {
                throw new \Exception("El proyecto con ID {$id} no existe.");
            }

            return ProyectoService::eliminarProyecto($id, $motivo, $folio);
        });
    }

    public static function cambiarStatus(int $id)
    {
        return DB::transaction(function () use ($id) {
            $folio = FolioService::obtener('log_proyectos');

            $proyecto = ProyectoService::obtenerPorId($id);
            if (!$proyecto) {
                throw new \Exception("El proyecto con ID {$id} no existe.");
            }

            return ProyectoService::cambiarStatus($id, $folio);
        });
    }

    public static function actualizarAsignacionesUsuarios(int $proyectoId, array $usuariosNuevos)
    {
        return DB::transaction(function () use ($proyectoId, $usuariosNuevos) {
            $folio = FolioService::obtener('log_proyectos');
            return ProyectoService::actualizarAsignacionesUsuarios($proyectoId, $usuariosNuevos, $folio);
        });
    }

    public static function ClientesDisponibles(array $filtros = [])
    {
        return ClienteService::listarClientes($filtros);
    }
}

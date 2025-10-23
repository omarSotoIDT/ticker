<?php

namespace App\Cordinators;

use App\Services\ProyectoService;
use App\Services\ClienteService;

class ProyectoCoordinator
{
   
    public static function crearProyecto(array $data)
    {
        $clienteId = $data['cliente_id'];

        $clientes = ClienteService::listarClientes(['cliente_id' => $clienteId]);
        if ($clientes->isEmpty()) {
            throw new \Exception("Cliente con ID {$clienteId} no existe o está eliminado.");
        }

        $proyectoId = ProyectoService::registrarProyecto($data);

        return $proyectoId;
    }

    public static function actualizarProyecto(int $id, array $data)
    {
        $proyectoActual = ProyectoRepoData::obtenerPorId($id);
        if (!$proyectoActual) {
            throw new \Exception("El proyecto con ID {$id} no existe.");
        }
    
        $clienteId = $data['cliente_id'];
        if ($clienteId) {
            $clientes = ClienteService::listarClientes(['cliente_id' => $clienteId]);
            if ($clientes->isEmpty()) {
                throw new \Exception("Cliente con ID {$clienteId} no existe o está eliminado.");
            }
        }
            $resultado = ProyectoService::actualizarProyecto($id, $data);
    
        return $resultado;
    }
    
    public static function ClientesDisponibles(array $filtros = [])
    {
        return ClienteService::listarClientes($filtros);
    }
}

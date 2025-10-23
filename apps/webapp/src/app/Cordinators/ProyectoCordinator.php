<?php

namespace App\Cordinators;

use App\Services\ProyectoService;
use App\Services\ClienteService;

class ProyectoCoordinator
{
   
    public static function crearProyectoConCliente(array $data)
    {
        $clienteId = $data['cliente_id'];

        $clientes = ClienteService::listarClientes(['cliente_id' => $clienteId]);
        if ($clientes->isEmpty()) {
            throw new \Exception("Cliente con ID {$clienteId} no existe o está eliminado.");
        }

        $proyectoId = ProyectoService::registrarProyecto($data);

        return $proyectoId;
    }


    public static function ClientesDisponibles(array $filtros = [])
    {
        return ClienteService::listarClientes($filtros);
    }
}

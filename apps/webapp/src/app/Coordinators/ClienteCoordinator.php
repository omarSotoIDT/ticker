<?php

namespace App\Coordinators;

use App\Services\ClienteService;

class ClienteCoordinator
{
    public static function obtenerClientes(array $filtros = [], bool $paginate = true)
    {
        $clientes = ClienteService::listarClientes($filtros, 10, $paginate);

        if ($paginate && $clientes instanceof \Illuminate\Pagination\LengthAwarePaginator) {
            return [
                'clientes' => $clientes->items(),
                'clientes_sin_paginar' => null,
                'links' => $clientes->linkCollection(),
            ];
        } else {
            return [
                'clientes' => null,
                'clientes_sin_paginar' => $clientes,
                'links' => null,
            ];
        }
    }

    public static function crearCliente(array $datos)
    {
        return ClienteService::registrarCliente($datos);
    }

    public static function actualizarCliente(int $id, array $datos)
    {
        return ClienteService::actualizarCliente($id, $datos);
    }

    public static function cambioStatus(int $id)
    {
        return ClienteService::cambioStatus($id);
    }

    public static function eliminarCliente(int $id, string $motivo)
    {
        return ClienteService::eliminarCliente($id, $motivo);
    }

    public static function obtenerNombre(int $id)
    {
        return ClienteService::obtenerNombre($id);
    }
}

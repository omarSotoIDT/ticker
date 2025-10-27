<?php

namespace App\Services;

use App\BO\ClienteBO;

use App\RepoAction\ClienteRepoAction;
use App\RepoData\ClienteRepoData;

class ClienteService
{
    public static function registrarCliente(array $data): int
    {
        $data = ClienteBO::armarInsert($data);
        return ClienteRepoAction::crearCliente($data);
    }

    public static function listarClientes(array $filters = [])
    {
        return ClienteRepoData::obtenerClientes($filters);
    }

    public static function actualizarCliente(int $id, array $data): bool
    {
        $data = ClienteBO::armarUpdate($data);
        return ClienteRepoAction::actualizarCliente($id, $data);
    }

    public static function activaCliente(int $id): bool
    {
        $cliente = ClienteRepoData::obtenerPorId($id);
        if (!$cliente) return false;

        return ClienteRepoAction::activaCliente($id, $cliente->status);
    }
   
    public static function eliminarCliente(int $id, string $motivo): bool
    {
        $cliente = ClienteRepoData::obtenerPorId($id);
        if (!$cliente) return false;

        return ClienteRepoAction::eliminarCliente($id, $motivo);
    }
}

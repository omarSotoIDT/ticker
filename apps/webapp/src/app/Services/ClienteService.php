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

    public static function listarClientes(array $filters = [], $limit = 10, bool $paginate = true)
    {
        return ClienteRepoData::obtenerClientes($filters, $limit, $paginate);
    }

    public static function actualizarCliente(int $id, array $data): bool
    {
        $data = ClienteBO::armarUpdate($data);
        return ClienteRepoAction::actualizarCliente($id, $data);
    }

    public static function cambioStatus(int $id): bool
    {
        $cliente = ClienteRepoData::obtenerPorId($id);
        if (!$cliente) return false;
        $cliente = ClienteBO::armarUpdateStatus($cliente->status);

        return ClienteRepoAction::cambiarStatus($id, $cliente);
    }
   
    public static function eliminarCliente(int $id, string $motivo): bool
    {
        $cliente = ClienteRepoData::obtenerPorId($id);
        if (!$cliente) return false;
        $motivo = ClienteBO::armarUpdateEliminacion($motivo);

        return ClienteRepoAction::eliminarCliente($id, $motivo);
    }

    public static function obtenerNombre(int $id): ?string
    {
        return ClienteRepoData::obtenerNombrePorId($id);
    }
}
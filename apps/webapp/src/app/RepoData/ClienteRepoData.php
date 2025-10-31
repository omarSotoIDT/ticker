<?php

namespace App\RepoData;

use Illuminate\Support\Facades\DB;
use App\RepoHelper\ClienteRepoHelper;

class ClienteRepoData
{
    public static function obtenerClientes(array $filters = [])
    {
        $query = DB::table('clientes')
            ->select('cliente_id', 'nombre', 'descripcion', 'contacto', 'email', 'status', 'registro_fecha')
            ->where('status', '!=', 'ELIMINADO');
        $query = ClienteRepoHelper::aplicarFiltros($query, $filters);

        return $query->get();
    }

    public static function obtenerPorId(int $id): ?object
    {
        return DB::table('clientes')->where('cliente_id', $id)->first();
    }

    public static function obtenerNombrePorId(int $id): ?string
    {
        $cliente = DB::table('clientes')->select('nombre')->where('cliente_id', $id)->first();
        return $cliente ? $cliente->nombre : null;
    }
}

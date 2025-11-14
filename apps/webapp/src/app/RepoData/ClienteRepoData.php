<?php

namespace App\RepoData;

use Illuminate\Support\Facades\DB;
use App\RepoHelper\ClienteRepoHelper;

class ClienteRepoData
{
    public static function obtenerClientes(array $filters = [], $limit = 10, $paginate)
    {
        $query = DB::table('clientes')
            ->select('cliente_id', 'nombre', 'descripcion', 'contacto', 'email', 'status', 'registro_fecha')
            ->where('status', '!=', 'ELIMINADO');

        $query = ClienteRepoHelper::aplicarFiltros($query, $filters);

        if ($paginate){
            $resultado = $query->paginate($limit);
        }
        else{
            $resultado = $query->get()->toArray();
        }
        return $resultado;
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
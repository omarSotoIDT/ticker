<?php

namespace App\RepoAction;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Consts\StatusConsts;

class ClienteRepoAction
{
    public static function crearCliente(array $data): int
    {
        $clienteId = DB::table('clientes')->insertGetId($data);

        return $clienteId;
    }


    public static function actualizarCliente(int $id, array $data): bool
    {
        $updated = DB::table('clientes')
            ->where('cliente_id', $id)
            ->update($data);

        return (bool) $updated;
    }
    
    public static function cambiarStatus(int $id, array $estadoActual): bool
{
    return DB::table('clientes')
        ->where('cliente_id', $id)
        ->update($estadoActual);
}

public static function eliminarCliente(int $id, array $motivo): bool
{
    return DB::table('clientes')
        ->where('cliente_id', $id)
        ->update($motivo);
}

}

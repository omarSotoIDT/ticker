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
    
    public static function activaCliente(int $id, string $estadoActual): bool
    {
        $nuevoEstado = $estadoActual === StatusConsts::ACTIVO
            ? StatusConsts::INACTIVO
            : StatusConsts::ACTIVO;
    
        $data = [
            'status' => $nuevoEstado,
            'actualizacion_autor_id' => Auth::id(),
            'actualizacion_fecha' => Carbon::now(),
        ];
    
        $updated = DB::table('clientes')
            ->where('cliente_id', $id)
            ->update($data);
    
        return (bool) $updated;
    }
    

    public static function eliminarCliente(int $id, string $motivo): bool
    {
        $data = [
            'status' => StatusConsts::ELIMINADO,
            'motivo_eliminacion' => $motivo,
            'actualizacion_autor_id' => Auth::id(),
            'actualizacion_fecha' => Carbon::now(),
        ];

        $updated = DB::table('clientes')
            ->where('cliente_id', $id)
            ->update($data);

        return (bool) $updated;
    }
}

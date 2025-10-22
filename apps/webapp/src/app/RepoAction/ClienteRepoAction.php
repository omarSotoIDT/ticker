<?php

namespace App\RepoAction;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ClienteRepoAction
{
    public static function crearCliente(array $data): int
    {
        $clienteId = DB::table('clientes')->insertGetId([
            'nombre'            => $data['nombre'],
            'descripcion'       => $data['descripcion'],
            'contacto'          => $data['contacto'],
            'email'             => $data['email'],
            'status'            => $data['status'] ?? 'ACTIVO',
            'registro_autor_id' => Auth::id(),
            'registro_fecha'    => Carbon::now(),
        ]);


        return $clienteId;
    }


    public static function actualizarCliente(int $id, array $data): bool
    {
        return DB::table('clientes')
            ->where('cliente_id', $id)
            ->update([
                'nombre'                => $data['nombre'],
                'descripcion'           => $data['descripcion'],
                'contacto'              => $data['contacto'],
                'email'                 => $data['email'],
                'actualizacion_autor_id' => Auth::id(),
                'actualizacion_fecha'   => Carbon::now(),
            ]);
    }

    public static function activaCliente(int $id, string $estadoActual): bool
    {
        $nuevoEstado = $estadoActual === 'ACTIVO' ? 'INACTIVO' : 'ACTIVO';

        $data = [
            'status' => $nuevoEstado,
            'actualizacion_autor_id' => Auth::id(),
            'actualizacion_fecha' => Carbon::now(),
        ];

        if ($nuevoEstado === 'INACTIVO') {
            $data['actualizacion_autor_id'] = Auth::id();
            $data['actualizacion_fecha'] = Carbon::now();
        } 

        return DB::table('clientes')
            ->where('cliente_id', $id)
            ->update($data);
    }

    public static function eliminarCliente(int $id, string $motivo): bool
    {
        $data = [
            'status' => 'ELIMINADO',
            'motivo_eliminacion' => $motivo,
            'actualizacion_autor_id' => auth::id(),
            'actualizacion_fecha' => now()
        ];

        return DB::table('clientes')
            ->where('cliente_id', $id)
            ->update($data);
    }
}

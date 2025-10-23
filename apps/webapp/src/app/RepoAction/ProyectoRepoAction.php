<?php

namespace App\RepoAction;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ProyectoRepoAction
{
    public static function crearProyecto(array $data): int
    {
        return DB::table('proyectos')->insertGetId([
            'cliente_id'         => $data['cliente_id'],
            'nombre'             => $data['nombre'],
            'descripcion'        => $data['descripcion'],
            'status'             => $data['status'] ?? 'ACTIVO',
            'registro_autor_id'  => Auth::id(),
            'registro_fecha'     => Carbon::now(),
        ]);
    }

    public static function actualizarProyecto(int $id, array $data): bool
    {
        return DB::table('proyectos')
            ->where('proyecto_id', $id)
            ->update([
                'cliente_id'           => $data['cliente_id'],
                'nombre'               => $data['nombre'],
                'descripcion'          => $data['descripcion'],
                'status'               => $data['status'] ?? 'ACTIVO',
                'actualizacion_autor_id' => Auth::id(),
                'actualizacion_fecha'  => Carbon::now(),
            ]);
    }

    public static function activaProyecto(int $id, string $estadoActual): bool
    {
        $nuevoEstado = $estadoActual === 'ACTIVO' ? 'INACTIVO' : 'ACTIVO';

        return DB::table('proyectos')
            ->where('proyecto_id', $id)
            ->update([
                'status'               => $nuevoEstado,
                'actualizacion_autor_id' => Auth::id(),
                'actualizacion_fecha'  => Carbon::now(),
            ]);
    }

    public static function eliminarProyecto(int $id, string $motivo): bool
    {
        return DB::table('proyectos')
            ->where('proyecto_id', $id)
            ->update([
                'status'               => 'ELIMINADO',
                'motivo_eliminacion'   => $motivo,
                'actualizacion_autor_id' => Auth::id(),
                'actualizacion_fecha'  => Carbon::now(),
            ]);
    }

    public static function registrarLog(int $proyectoId, string $accion, string $descripcion): void
    {
        $folio = 'FOLIO-' . strtoupper(substr(sha1(time() . $proyectoId), 0, 6));

        DB::table('log_proyectos')->insert([
            'proyecto_id'    => $proyectoId,
            'usuario_id'     => Auth::id(),
            'folio'          => $folio,
            'descripcion'    => "$accion: $descripcion",
            'registro_fecha' => Carbon::now(),
        ]);
    }
}

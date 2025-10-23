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

    public static function asignarUsuarios(int $proyectoId, array $usuarios)
    {
        foreach ($usuarios as $usuarioId) {
            DB::table('rel_usuarios_proyectos')->updateOrInsert(
                ['usuario_id' => $usuarioId, 'proyecto_id' => $proyectoId],
                [
                    'status' => 'ACTIVO',
                    'registro_autor_id' => Auth::id(),
                    'registro_fecha' => Carbon::now(),
                ]
            );
        }
    }

    public static function actualizarUsuariosAsignados(
        int $proyectoId,
        array $usuariosAgregados,
        array $usuariosEliminados
    ) {
        $ahora = now();
        $usuarioActual = Auth::id();
    
        foreach ($usuariosAgregados as $uid) {
            DB::table('rel_usuarios_proyectos')->updateOrInsert(
                ['usuario_id' => $uid, 'proyecto_id' => $proyectoId],
                [
                    'status' => 'ACTIVO',
                    'registro_autor_id' => $usuarioActual,
                    'registro_fecha' => $ahora,
                    'actualizacion_autor_id' => $usuarioActual,
                    'actualizacion_fecha' => $ahora,
                ]
            );
        }
    
        foreach ($usuariosEliminados as $uid) {
            DB::table('rel_usuarios_proyectos')
                ->where('usuario_id', $uid)
                ->where('proyecto_id', $proyectoId)
                ->update([
                    'status' => 'ELIMINADO',
                    'actualizacion_autor_id' => $usuarioActual,
                    'actualizacion_fecha' => $ahora,
                ]);
        }
    }
    
}

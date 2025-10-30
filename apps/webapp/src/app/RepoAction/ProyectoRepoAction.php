<?php

namespace App\RepoAction;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Consts\StatusConsts;

class ProyectoRepoAction
{
    public static function crearProyecto(array $insertData): int
    {
        $insertData['registro_autor_id'] = Auth::id();
        $insertData['registro_fecha'] = Carbon::now();

        return DB::table('proyectos')->insertGetId($insertData);
    }

    public static function actualizarProyecto(int $id, array $updateData): bool
    {
        $updateData['actualizacion_autor_id'] = Auth::id();
        $updateData['actualizacion_fecha'] = Carbon::now();

        return DB::table('proyectos')
            ->where('proyecto_id', $id)
            ->update($updateData);
    }

    public static function cambiarStatus(int $id, string $estadoActual): bool
    {
        $nuevoEstado = $estadoActual === StatusConsts::ACTIVO ? StatusConsts::INACTIVO : StatusConsts::ACTIVO;

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
                'status'               => StatusConsts::ELIMINADO,
                'motivo_eliminacion'   => $motivo,
                'actualizacion_autor_id' => Auth::id(),
                'actualizacion_fecha'  => Carbon::now(),
            ]);
    }

    public static function asignarUsuarios(int $proyectoId, array $usuarios)
    {
        foreach ($usuarios as $usuarioId) {
            DB::table('rel_usuarios_proyectos')->updateOrInsert(
                ['usuario_id' => $usuarioId, 'proyecto_id' => $proyectoId],
                [
                    'status' => StatusConsts::ACTIVO,
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
        $ahora = Carbon::now();
        $usuarioActual = Auth::id();
    
        foreach ($usuariosAgregados as $uid) {
            DB::table('rel_usuarios_proyectos')->updateOrInsert(
                ['usuario_id' => $uid, 'proyecto_id' => $proyectoId],
                [
                    'status' => StatusConsts::ACTIVO,
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
                    'status' => StatusConsts::ELIMINADO,
                    'actualizacion_autor_id' => $usuarioActual,
                    'actualizacion_fecha' => $ahora,
                ]);
        }
    }

    public static function crearLog(array $log)
    {
        return DB::table('log_proyectos')->insert($log);
    }
    
    
}

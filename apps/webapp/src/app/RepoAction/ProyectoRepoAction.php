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

    public static function actualizarUsuariosAsignados($acciones) 
    {
        foreach ($acciones['inserciones'] as $accion) {
            DB::table('rel_usuarios_proyectos')->updateOrInsert(
                $accion['filtros'],
                $accion['valores']
            );
        }

        foreach ($acciones['actualizaciones'] as $accion) {
            DB::table('rel_usuarios_proyectos')
                ->where($accion['filtros'])
                ->update($accion['valores']);
        }
    }
    
    public static function crearLog(array $log)
    {
        return DB::table('log_proyectos')->insert($log);
    }
    
    
}

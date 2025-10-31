<?php

namespace App\BO;

use App\Consts\StatusConsts;
use Illuminate\Support\Facades\Auth;

class ProyectoBO
{
    public static function armarInsert(array $data): array
    {

        return [
            'cliente_id'  => (int) $data['cliente_id'],
            'nombre'      => $data['nombre'],
            'descripcion' => $data['descripcion'],
            'status'      => StatusConsts::ACTIVO,
        ];
    }

    public static function armarUpdate(array $data): array
    {

        return [
            'cliente_id'  => (int) $data['cliente_id'],
            'nombre'      => $data['nombre'],
            'descripcion' => $data['descripcion'],
        ];
    }

    public static function prepararAsignaciones(array $usuariosIds): array
    {
        $usuarios = [];

        foreach ($usuariosIds as $uid) {
            $usuarios[] = [
                'usuario_id' => (int) $uid,
                'status'     => StatusConsts::ACTIVO,
            ];
        }

        return $usuarios;
    }

    public static function armarInsertLog(array $datos): array
    {
        return [
            'proyecto_id'    => $datos['proyectoId'],
            'usuario_id'     => Auth::id(),
            'folio'          => $datos['folio'], 
            'descripcion'    => $datos['descripcion'],
            'registro_fecha' => now(),
        ];
    }
    
    public static function construirUsuariosAsignados(
         $proyectoId,
         $usuariosAgregados,
         $usuariosEliminados
    ): array {
        $ahora = now();
        $usuarioActual = Auth::id();

        $acciones = [
            'inserciones' => [],
            'actualizaciones' => [],
        ];

        foreach ($usuariosAgregados as $uid) {
            $acciones['inserciones'][] = [
                'filtros' => ['usuario_id' => $uid, 'proyecto_id' => $proyectoId],
                'valores' => [
                    'status' => StatusConsts::ACTIVO,
                    'registro_autor_id' => $usuarioActual,
                    'registro_fecha' => $ahora,
                    'actualizacion_autor_id' => $usuarioActual,
                    'actualizacion_fecha' => $ahora,
                ],
            ];
        }

        foreach ($usuariosEliminados as $uid) {
            $acciones['actualizaciones'][] = [
                'filtros' => ['usuario_id' => $uid, 'proyecto_id' => $proyectoId],
                'valores' => [
                    'status' => StatusConsts::ELIMINADO,
                    'actualizacion_autor_id' => $usuarioActual,
                    'actualizacion_fecha' => $ahora,
                ],
            ];
        }

        return $acciones;
    }

    public static function armarUpdateStatus(string $estadoActual): array
    {
        $nuevoEstado = $estadoActual === StatusConsts::ACTIVO
            ? StatusConsts::INACTIVO
            : StatusConsts::ACTIVO;
    
        return [
            'status' => $nuevoEstado,
            'actualizacion_autor_id' => Auth::id(),
            'actualizacion_fecha' => now(),
        ];
    }
    
    
    public static function armarUpdateEliminacion(string $motivo): array
    {
        return [
            'status' => StatusConsts::ELIMINADO,
            'motivo_eliminacion' => $motivo,
            'actualizacion_autor_id' => Auth::id(),
            'actualizacion_fecha' => now(),
        ];
    }

}

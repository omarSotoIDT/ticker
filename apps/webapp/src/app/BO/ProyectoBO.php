<?php

namespace App\BO;

use App\Consts\StatusConsts;
use Illuminate\Support\Facades\Auth;

class ProyectoBO
{
    public static function prepararDatos(array $data): array
    {
        return [
            'cliente_id'  => $data['cliente_id'],
            'nombre'      => $data['nombre'],
            'descripcion' => $data['descripcion'],
        ];
    }

    public static function armarInsert(array $data): array
    {
        $prepared = self::prepararDatos($data);

        return [
            'cliente_id'  => (int) $prepared['cliente_id'],
            'nombre'      => $prepared['nombre'],
            'descripcion' => $prepared['descripcion'],
            'status'      => StatusConsts::ACTIVO,
        ];
    }

    public static function armarUpdate(array $data): array
    {
        $prepared = self::prepararDatos($data);

        return [
            'cliente_id'  => (int) $prepared['cliente_id'],
            'nombre'      => $prepared['nombre'],
            'descripcion' => $prepared['descripcion'],
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
}

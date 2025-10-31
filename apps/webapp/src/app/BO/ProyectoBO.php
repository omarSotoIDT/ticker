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
}

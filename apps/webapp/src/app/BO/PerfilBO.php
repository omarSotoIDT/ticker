<?php

namespace App\BO;

use App\Consts\StatusConsts;
use Illuminate\Support\Facades\Auth;

class PerfilBO
{
    public static function armarInsert(array $datos)
    {
        return [
            'clave' => $datos['clave'],
            'nombre' => $datos['nombre'],
            'descripcion' => $datos['descripcion'],
            'status' => StatusConsts::ACTIVO,
            'super_usuario' => $datos['super_usuario'],
            'registro_autor_id' => Auth::id(),
            'registro_fecha' => now(),
            'permisos' => $datos['permisos'] ?? []
        ];
    }

    public static function armarUpdate(array $datos)
    {
        return [
            'clave' => $datos['clave'],
            'nombre' => $datos['nombre'],
            'descripcion' => $datos['descripcion'],
            'status' => $datos['status'] ?? StatusConsts::ACTIVO,
            'super_usuario' => $datos['super_usuario'],
            'actualizacion_autor_id' => Auth::id(),
            'actualizacion_fecha' => now(),
            'permisos' => $datos['permisos'] ?? []
        ];
    }

    public static function armarDelete()
    {
        return [
            'status' => StatusConsts::ELIMINADO,
            'actualizacion_autor_id' => Auth::id(),
            'actualizacion_fecha' => now()
        ];
    }

    public static function prepararPermisos(int $perfil_id, array $permisos): array
    {
        $permisosValidos = array_filter($permisos);

        $resultado = [];
        foreach ($permisosValidos as $permiso_id) {
            $resultado[] = [
                'perfil_id' => $perfil_id,
                'permiso_id' => $permiso_id,
                'registro_autor_id' => Auth::id(),
                'registro_fecha' => now()
            ];
        }

        return $resultado;
    }
}

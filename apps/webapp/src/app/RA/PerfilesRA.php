<?php

namespace App\RA;

use Illuminate\Support\Facades\DB;

class PerfilesRA
{
    public static function crearPerfil(array $datos)
    {
        $id = DB::table('sys_perfiles')->insertGetId([
            'clave' => $datos['clave'],
            'nombre' => $datos['nombre'],
            'descripcion' => $datos['descripcion'],
            'status' => $datos['status'],
            'super_usuario' => $datos['super_usuario'] ?? 0, 
            'registro_autor_id' => $datos['registro_autor_id'],
            'registro_fecha' => $datos['registro_fecha']
        ]);
        return $id;
    }

    public static function actualizarPerfil($perfil_id, array $datos)
    {
        $camposActualizar = [];
        if (isset($datos['clave'])) $camposActualizar['clave'] = $datos['clave'];
        if (isset($datos['nombre'])) $camposActualizar['nombre'] = $datos['nombre'];
        if (isset($datos['descripcion'])) $camposActualizar['descripcion'] = $datos['descripcion'];
        if (isset($datos['status'])) $camposActualizar['status'] = $datos['status'];
        if (isset($datos['super_usuario'])) $camposActualizar['super_usuario'] = $datos['super_usuario'];
        if (isset($datos['actualizacion_autor_id'])) $camposActualizar['actualizacion_autor_id'] = $datos['actualizacion_autor_id'];
        if (isset($datos['actualizacion_fecha'])) $camposActualizar['actualizacion_fecha'] = $datos['actualizacion_fecha'];

        DB::table('sys_perfiles')
            ->where('perfil_id', $perfil_id)
            ->update($camposActualizar);
    }

    public static function sincronizarPermisos($perfil_id, array $permisos)
    {
        DB::table('rel_perfiles_permisos')->where('perfil_id', $perfil_id)->delete();

        $permisosValidos = array_filter($permisos);

        if (!empty($permisosValidos)) {
            foreach ($permisosValidos as $permiso_id) {
                DB::table('rel_perfiles_permisos')->insert([
                    'perfil_id' => $perfil_id,
                    'permiso_id' => $permiso_id,
                    'registro_autor_id' => session('user_id') ?? 1,
                    'registro_fecha' => now()
                ]);
            }
        }
    }
}

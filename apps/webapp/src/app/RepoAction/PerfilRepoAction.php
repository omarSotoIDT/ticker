<?php

namespace App\RepoAction;

use Illuminate\Support\Facades\DB;

class PerfilRepoAction
{
    public static function crearPerfil(array $datos)
    {
        $permisos = $datos['permisos'] ?? [];

        $allowed = ['clave', 'nombre', 'descripcion', 'status', 'super_usuario', 'registro_autor_id', 'registro_fecha'];
        $insertData = array_intersect_key($datos, array_flip($allowed));

        $id = DB::table('sys_perfiles')->insertGetId($insertData);

        if (!empty($permisos)) {
            self::sincronizarPermisos($id, $permisos);
        }

        return $id;
    }

    public static function actualizarPerfil($perfil_id, array $datos)
    {
        $allowed = ['clave', 'nombre', 'descripcion', 'status', 'super_usuario', 'actualizacion_autor_id', 'actualizacion_fecha'];
        $camposActualizar = array_intersect_key($datos, array_flip($allowed));

        if (!empty($camposActualizar)) {
            DB::table('sys_perfiles')
                ->where('perfil_id', $perfil_id)
                ->update($camposActualizar);
        }

        $permisos = $datos['permisos'] ?? [];
        if (!empty($permisos)) {
            self::sincronizarPermisos($perfil_id, $permisos);
        }
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
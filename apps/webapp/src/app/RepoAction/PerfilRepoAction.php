<?php

namespace App\RepoAction;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
class PerfilRepoAction
{
    public static function crearPerfil(array $datosArmados)
    {
        $insertData = $datosArmados;
        unset($insertData['permisos']);

        $id = DB::table('sys_perfiles')->insertGetId($insertData);

        if (!empty($datosArmados['permisos'])) {
            self::sincronizarPermisos($id, $datosArmados['permisos']);
        }

        return $id;
    }

    public static function actualizarPerfil(int $perfil_id, array $datosArmados)
    {
        $updateData = $datosArmados;
        unset($updateData['permisos']); 

        DB::table('sys_perfiles')->where('perfil_id', $perfil_id)->update($updateData);

        if (!empty($datosArmados['permisos'])) {
            self::sincronizarPermisos($perfil_id, $datosArmados['permisos']);
        }
    }

    public static function sincronizarPermisos(int $perfil_id, array $permisos)
    {
        DB::table('rel_perfiles_permisos')->where('perfil_id', $perfil_id)->delete();

        $permisosValidos = array_filter($permisos);

        foreach ($permisosValidos as $permiso_id) {
            DB::table('rel_perfiles_permisos')->insert([
                'perfil_id' => $perfil_id,
                'permiso_id' => $permiso_id,
                'registro_autor_id' => Auth::id(), 
                'registro_fecha' => now()
            ]);
        }
    }
}

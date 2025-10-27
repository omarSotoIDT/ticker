<?php

namespace App\RepoAction;

use Illuminate\Support\Facades\DB;

class PerfilRepoAction
{
    public static function crearPerfil(array $datosArmados)
    {
        $insertData = $datosArmados;
        unset($insertData['permisos']);

        $id = DB::table('sys_perfiles')->insertGetId($insertData);

        if (!empty($datosArmados['permisos'])) {
            $permisosData = \App\BO\PerfilBO::prepararPermisos($id, $datosArmados['permisos']);
            DB::table('rel_perfiles_permisos')->insert($permisosData);
        }

        return $id;
    }

    public static function actualizarPerfil(int $perfil_id, array $datosArmados)
    {
        $updateData = $datosArmados;
        unset($updateData['permisos']); 

        DB::table('sys_perfiles')->where('perfil_id', $perfil_id)->update($updateData);

        if (!empty($datosArmados['permisos'])) {
            $permisosData = \App\BO\PerfilBO::prepararPermisos($perfil_id, $datosArmados['permisos']);
            DB::table('rel_perfiles_permisos')->where('perfil_id', $perfil_id)->delete();
            DB::table('rel_perfiles_permisos')->insert($permisosData);
        }
    }
}

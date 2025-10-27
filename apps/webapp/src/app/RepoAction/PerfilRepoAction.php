<?php

namespace App\RepoAction;

use Illuminate\Support\Facades\DB;

class PerfilRepoAction
{
    public static function crearPerfil(array $datosPerfil, array $permisos = [])
    {
        $perfil_id = DB::table('sys_perfiles')->insertGetId($datosPerfil);

        if (!empty($permisos)) {
            DB::table('rel_perfiles_permisos')->insert($permisos);
        }

        return $perfil_id;
    }

    public static function actualizarPerfil(int $perfil_id, array $datosPerfil = [], array $permisos = [])
    {
        if (!empty($datosPerfil)) {
            DB::table('sys_perfiles')->where('perfil_id', $perfil_id)->update($datosPerfil);
        }

        if (!empty($permisos)) {
            DB::table('rel_perfiles_permisos')->where('perfil_id', $perfil_id)->delete();
            DB::table('rel_perfiles_permisos')->insert($permisos);
        }
    }
}

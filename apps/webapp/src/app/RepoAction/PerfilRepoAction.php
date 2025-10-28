<?php

namespace App\RepoAction;

use Illuminate\Support\Facades\DB;

class PerfilRepoAction
{
    public static function crearPerfil(array $datosPerfil)
    {
        return DB::table('sys_perfiles')->insertGetId($datosPerfil);
    }

    public static function actualizarPerfil(int $perfil_id, array $datosPerfil = [])
    {
        DB::table('sys_perfiles')->where('perfil_id', $perfil_id)->update($datosPerfil);
    }
}

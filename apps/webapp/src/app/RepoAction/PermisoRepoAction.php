<?php

namespace App\RepoAction;

use Illuminate\Support\Facades\DB;

class PermisoRepoAction
{
    public static function agregarPermisos(int $perfil_id, array $rows): void
    {
        DB::table('rel_perfiles_permisos')->where('perfil_id', $perfil_id)->delete();
        DB::table('rel_perfiles_permisos')->insert($rows);
    }

}
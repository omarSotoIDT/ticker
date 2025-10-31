<?php

namespace App\RepoData;

use Illuminate\Support\Facades\DB;

class PermisoRepoData
{
    public static function obtenerPermisos()
    {
        return DB::table('sys_permisos')->get();
    }
}
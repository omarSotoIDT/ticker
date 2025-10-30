<?php

namespace App\RepoAction;

use Illuminate\Support\Facades\DB;

class UsuarioRepoAction
{
    public static function crear($usuario) {
        return DB::table('sys_usuarios')->insertGetId($usuario);
    }

    public static function actualizar($id, $usuario) {
        return DB::table('sys_usuarios')->where('usuario_id', $id)->update($usuario);
    }

    public static function marcarAcceso($id) {
        return DB::table('sys_usuarios')->where('usuario_id', $id)->update(['ultimo_acceso_fecha' => now()]);
    }
}

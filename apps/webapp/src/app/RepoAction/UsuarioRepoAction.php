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

    public static function sincronizarPerfiles($usuario_id, $permisos) {
        DB::table('rel_usuarios_perfiles')->where('usuario_id', $usuario_id)->delete();
        return DB::table('rel_usuarios_perfiles')->insert($permisos);
    }
}

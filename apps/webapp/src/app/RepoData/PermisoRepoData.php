<?php

namespace App\RepoData;

use Illuminate\Support\Facades\DB;

class PermisoRepoData
{
    public static function obtenerPermisos()
    {
        return DB::table('sys_permisos')->get();
    }

    public static function obtenerPermisosPerfiles(array $perfilIds)
    {
        return DB::table('rel_perfiles_permisos')
            ->whereIn('perfil_id', $perfilIds)
            ->select('perfil_id', 'permiso_id')
            ->get()
            ->groupBy('perfil_id');
    }

    public static function obtenerPerfilUsuario(int $usuarioId)
    {
        return DB::table('rel_usuarios_perfiles')
            ->where('usuario_id', $usuarioId)
            ->value('perfil_id');
    }

    public static function esSuperUsuario(int $perfilId): bool
    {
        return DB::table('sys_perfiles')
            ->where('perfil_id', $perfilId)
            ->where('super_usuario', 1)
            ->exists();
    }

    public static function obtenerPermisosPorPerfil(int $perfilId): array
    {
        return DB::table('rel_perfiles_permisos')
            ->join('sys_permisos', 'rel_perfiles_permisos.permiso_id', '=', 'sys_permisos.permiso_id')
            ->where('rel_perfiles_permisos.perfil_id', $perfilId)
            ->pluck('sys_permisos.codigo')
            ->toArray();
    }
}
<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

if (!function_exists('tienePermiso')) {

    function tienePermiso(int $usuarioId, string $codigoPermiso): bool
    {
        $perfilId = DB::table('rel_usuarios_perfiles')
            ->where('usuario_id', $usuarioId)
            ->value('perfil_id');

        if (!$perfilId) {
            return false;
        }

        $esSuperUsuario = DB::table('sys_perfiles')
            ->where('perfil_id', $perfilId)
            ->where('super_usuario', 1)
            ->exists();

        if ($esSuperUsuario) {
            return true;
        }

        $permisos = DB::table('rel_perfiles_permisos')
            ->join('sys_permisos', 'rel_perfiles_permisos.permiso_id', '=', 'sys_permisos.permiso_id')
            ->where('rel_perfiles_permisos.perfil_id', $perfilId)
            ->pluck('sys_permisos.codigo')
            ->toArray();

        return in_array($codigoPermiso, $permisos);

        if (!function_exists('can')) {
            function can(string $codigoPermiso): bool
            {
                $userId = Auth::id();
                return $userId ? tienePermiso($userId, $codigoPermiso) : false;
            }
        }
    }
}

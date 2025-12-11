<?php

namespace App\BO;

use Illuminate\Support\Facades\Auth;

class PermisoBO
{
    public static function prepararPermisos(int $perfil_id, array $permisos): array
    {
        return array_map(fn($permiso_id) => [
            'perfil_id' => $perfil_id,
            'permiso_id' => $permiso_id,
            'registro_autor_id' => Auth::id(),
            'registro_fecha' => now()
        ], $permisos);
    }
}

<?php

namespace App\Coordinators;

use App\Services\UsuarioService;

class UsuarioCoordinator
{
    public static function listar($filtros) {
        $usuarios = UsuarioService::listar($filtros, 'usuarioId,usuario,email,nombrePerfil,status,acceso');
        return $usuarios;
    }

    public static function agregar($data) {
        return UsuarioService::agregar($data);
    }
}

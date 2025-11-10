<?php

namespace App\Coordinators;

use App\Services\PerfilService;
use App\Services\UsuarioService;

class UsuarioCoordinator
{
    public static function cargarGestor()
    {
        $perfiles = PerfilService::obtenerPerfiles();
        return ['perfiles' => $perfiles];
    }

    public static function listar($filtros)
    {
        $usuarios = UsuarioService::listar($filtros, 'usuarioId,usuario,email,status,acceso,idPerfiles,nombrePerfiles');
        return $usuarios;
    }

    public static function agregar($data)
    {
        return UsuarioService::agregar($data);
    }
}

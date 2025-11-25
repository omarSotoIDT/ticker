<?php

namespace App\Coordinators;

use App\Services\PerfilService;
use App\Services\TicketService;
use App\Consts\TicketConsts;
use App\Services\UsuarioService;

class UsuarioCoordinator
{
    public static function cargarGestor()
    {
        $resultado = PerfilService::obtenerPerfiles([], 10, false);
        
        return ['perfiles' => $resultado];
    }

    public static function obtenerUsuarios(array $filtros = [], bool $paginate = true)
    {
        $usuarios = UsuarioService::listar($filtros, 'usuarioId,usuario,email,status,acceso,idPerfiles,nombrePerfiles', [], 10, null, $paginate);

        if ($paginate && $usuarios instanceof \Illuminate\Pagination\LengthAwarePaginator) {
            return [
                'usuarios' => $usuarios->items(),
                'usuarios_sin_paginar' => null,
                'links' => $usuarios->linkCollection(),
            ];
        } else {
            return [
                'usuarios' => null,
                'usuarios_sin_paginar' => $usuarios,
                'links' => null,
            ];
        }
    }

    public static function listar($filtros)
    {
        $resultado = self::obtenerUsuarios($filtros, false);
        return $resultado['usuarios_sin_paginar'];
    }

    public static function listarUsuarios($filtros)
    {
        $usuarios = UsuarioService::listarUsuarios($filtros, 'usuarioId,usuario,email,status,acceso,idPerfiles,nombrePerfiles');
        return $usuarios;
    }

    public static function agregar($data)
    {
        return UsuarioService::agregar($data);
    }

    public static function eliminar($id, $datos)
    {
        $ticketsActivos = TicketService::listar(
            [
                'usuario_asignado_id' => $id,
                'status_excluidos' => [TicketConsts::CERRADO, TicketConsts::CANCELADO]
            ],
            'ticketId'
        );

        if (count($ticketsActivos) > 0) {
            throw new \Exception(
                "No se puede eliminar el usuario porque tiene " . count($ticketsActivos) . " ticket(s) asignado(s) que no están cerrados o cancelados."
            );
        }

        return UsuarioService::eliminar($id, $datos);
    }
}

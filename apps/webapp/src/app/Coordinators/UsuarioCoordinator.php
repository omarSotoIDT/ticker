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

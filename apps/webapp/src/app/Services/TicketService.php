<?php

namespace App\Services;

use App\BO\TicketBO;
use App\RepoAction\TicketRepoAction;
use App\RepoData\TicketRepoData;

class TicketService
{
    public static function listar($filtros = [], $columnas = '', $orden = [], $limit = null, $offset = null)
    {
        $tickets = TicketRepoData::listar($filtros, $columnas, $orden, $limit, $offset);
        return $tickets;
    }

    public static function obtener($id, $columnas = '')
    {
        return TicketRepoData::obtener($id, $columnas);
    }

    public static function agregar($datos)
    {
        $insertTicket = TicketBO::armarInsert($datos);
        return TicketRepoAction::crear($insertTicket);
    }

    public static function editar(
        int $id,
        array $data,
        ?string $nombreUsuarioAnterior = null,
        ?string $nombreUsuarioNuevo = null,
        int $folio
    ) {
        $ticketActual = TicketService::obtener($id, 'titulo,descripcion,prioridad,status,usuarioAsignadoId');
        if (!$ticketActual) {
            throw new \Exception("El ticket con ID {$id} no existe.");
        }

        $anterior = (array) $ticketActual;
        if (array_key_exists('usuarioAsignadoId', $anterior)) {
            $anterior['usuario_asignado_id'] = $anterior['usuarioAsignadoId'];
            unset($anterior['usuarioAsignadoId']);
        }

        $cambios = [];
        $camposComparar = ['titulo', 'descripcion', 'prioridad', 'status', 'usuario_asignado_id'];

        foreach ($camposComparar as $campo) {
            if (array_key_exists($campo, $data) && array_key_exists($campo, $anterior)) {
                $valorAnterior = $anterior[$campo];
                $valorNuevo = $data[$campo];

                if ($valorAnterior !== $valorNuevo) {
                    if ($campo === 'usuario_asignado_id') {
                        $cambios[] = "Usuario asignado cambiado de '{$nombreUsuarioAnterior}' a '{$nombreUsuarioNuevo}'";
                    } else {
                        $cambios[] = ucfirst($campo) . " cambiado de '{$valorAnterior}' a '{$valorNuevo}'";
                    }
                }
            }
        }

        if (empty($cambios)) {
            return false;
        }

        $updateData = TicketBO::armarUpdate($data);
        $resultado = TicketRepoAction::actualizar($id, $updateData);

        $descripcion = "Ticket '{$anterior['titulo']}' actualizado:\n" . implode("\n", $cambios);

        self::agregarLog($id, $folio, $descripcion);

        return $resultado;
    }

    public static function editarEstado($id, $datos)
    {
        $updateTicket = TicketBO::armarUpdateEstado($datos);
        return TicketRepoAction::actualizar($id, $updateTicket);
    }

    public static function editarPrioridad($id, $datos)
    {
        $updateTicket = TicketBO::armarUpdatePrioridad($datos);
        return TicketRepoAction::actualizar($id, $updateTicket);
    }

    public static function editarAsignacion($id, $datos)
    {
        $updateTicket = TicketBO::armarUpdateAsignacion($datos);
        return TicketRepoAction::actualizar($id, $updateTicket);
    }

    public static function agregarLog(int $ticketId, int $folio, string $descripcion)
    {
        $insertLog = TicketBO::armarInsertLog([
            'ticket_id' => $ticketId,
            'folio' => $folio,
            'descripcion' => $descripcion,
        ]);
        return TicketRepoAction::crearLog($insertLog);
    }

    public static function obtenerLogs($id, $columnas = '', $orden = []) {
        $ticketLogs = TicketRepoData::obtenerLogs($id, $columnas, $orden);
        return $ticketLogs;
    }
}

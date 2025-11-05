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
        $id,
        $data,
        $nombreUsuarioAnterior = null,
        $nombreUsuarioNuevo = null,
        $nombreClienteAnterior = null,
        $nombreClienteNuevo = null,
        $nombreProyectoAnterior = null,
        $nombreProyectoNuevo = null,
        $nombreEtiquetaAnterior = null,
        $nombreEtiquetaNuevo = null,
        $folio
    ) {
        $ticketActual = TicketService::obtener(
            $id,
            'titulo,descripcion,prioridad,status,usuarioAsignadoId,clienteId,proyectoId,etiquetaId'
        );
    
        if (!$ticketActual) {
            throw new \Exception("El ticket con ID {$id} no existe.");
        }
    
        $anterior = (array) $ticketActual;
    
        // Normalizamos los nombres de campos
        $map = [
            'usuarioAsignadoId' => 'usuario_asignado_id',
            'clienteId'         => 'cliente_id',
            'proyectoId'        => 'proyecto_id',
            'etiquetaId'        => 'etiqueta_id',
        ];
    
        foreach ($map as $from => $to) {
            if (isset($anterior[$from])) {
                $anterior[$to] = $anterior[$from];
                unset($anterior[$from]);
            }
        }
    
        $camposComparar = [
            'titulo',
            'descripcion',
            'prioridad',
            'status',
            'usuario_asignado_id',
            'cliente_id',
            'proyecto_id',
            'etiqueta_id',
        ];
    
        $etiquetas = [
            'usuario_asignado_id' => ['label' => 'Usuario asignado', 'ant' => $nombreUsuarioAnterior, 'nvo' => $nombreUsuarioNuevo],
            'cliente_id'          => ['label' => 'Cliente',           'ant' => $nombreClienteAnterior, 'nvo' => $nombreClienteNuevo],
            'proyecto_id'         => ['label' => 'Proyecto',          'ant' => $nombreProyectoAnterior, 'nvo' => $nombreProyectoNuevo],
            'etiqueta_id'         => ['label' => 'Etiqueta',          'ant' => $nombreEtiquetaAnterior, 'nvo' => $nombreEtiquetaNuevo],
        ];
    
        $cambios = [];
    
        foreach ($camposComparar as $campo) {
            if (array_key_exists($campo, $data) && array_key_exists($campo, $anterior)) {
                $valorAnterior = $anterior[$campo];
                $valorNuevo = $data[$campo];
    
                if ($valorAnterior != $valorNuevo) {
                    if (isset($etiquetas[$campo])) {
                        $info = $etiquetas[$campo];
                        $cambios[] = "{$info['label']} cambiado de '{$info['ant']}' a '{$info['nvo']}'";
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
    
        if ($resultado === 0) {
            throw new \Exception("No se pudo actualizar el ticket o no hubo cambios en BD.");
        }
    
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

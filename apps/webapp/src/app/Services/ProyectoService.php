<?php

namespace App\Services;

use App\RepoAction\ProyectoRepoAction;
use App\RepoData\ProyectoRepoData;
use App\BO\ProyectoBO;
use App\Consts\StatusConsts;

class ProyectoService
{
    public static function listar(array $filtros = [])
    {
        return ProyectoRepoData::obtenerProyectos($filtros);
    }

    public static function registrarProyecto(array $data, string $clienteNombre, int $folio)
    {
        $insertData = ProyectoBO::armarInsert($data);
        $proyectoId = ProyectoRepoAction::crearProyecto($insertData);

        $nombreProyecto = $data['nombre'];
        $descripcion = "Proyecto creado: '{$nombreProyecto}' para cliente: '{$clienteNombre}'";
        $logData = [
            'proyectoId'  => $proyectoId,
            'folio'       => $folio,
            'descripcion' => $descripcion,
        ];

        self::agregarLog($logData);
        return $proyectoId;
    }

    public static function actualizarProyecto(
        int $id,
        array $data,
        ?string $nombreClienteAnterior = null,
        ?string $nombreClienteNuevo = null,
        int $folio
    ) {
        $proyectoActual = ProyectoRepoData::obtenerPorId($id);
        if (!$proyectoActual) {
            throw new \Exception("El proyecto con ID {$id} no existe.");
        }

        $anterior = (array) $proyectoActual;
        $cambios = [];
        $camposComparar = ['nombre', 'descripcion', 'cliente_id', 'status'];

        foreach ($camposComparar as $campo) {
            if (array_key_exists($campo, $data)) {
                $valorAnterior = $anterior[$campo];
                $valorNuevo = $data[$campo];
                if ($valorAnterior !== $valorNuevo) {
                    if ($campo === 'cliente_id') {
                        $cambios[] = "Cliente cambiado de '{$nombreClienteAnterior}' a '{$nombreClienteNuevo}'";
                    } else {
                        $cambios[] = ucfirst($campo) . " cambiado de '{$valorAnterior}' a '{$valorNuevo}'";
                    }
                }
            }
        }

        if (empty($cambios)) {
            return false;
        }

        $updateData = ProyectoBO::armarUpdate($data);
        $resultado = ProyectoRepoAction::actualizarProyecto($id, $updateData);

        $descripcion = "Proyecto '{$anterior['nombre']}' actualizado:\n" . implode("\n", $cambios);

        self::agregarLog([
            'proyectoId'  => $id,
            'folio'       => $folio,
            'descripcion' => $descripcion,
        ]);

        return $resultado;
    }

    public static function eliminarProyecto(int $id, string $motivo, int $folio)
    {
        $proyecto = ProyectoRepoData::obtenerPorId($id);
        if (!$proyecto) {
            throw new \Exception("El proyecto con ID {$id} no existe.");
        }

        $nombreProyecto = $proyecto->nombre ?? '';
        $resultado = ProyectoRepoAction::eliminarProyecto($id, $motivo);

        $descripcion = "Proyecto '{$nombreProyecto}' eliminado. Motivo: {$motivo}";

        self::agregarLog([
            'proyectoId'  => $id,
            'folio'       => $folio,
            'descripcion' => $descripcion,
        ]);

        return $resultado;
    }


    public static function cambiarStatus(int $id, int $folio)
    {
        $proyecto = ProyectoRepoData::obtenerPorId($id);
        if (!$proyecto) {
            throw new \Exception("El proyecto con ID {$id} no existe.");
        }

        $nuevoEstado = $proyecto
        ->status === StatusConsts::ACTIVO 
        ? StatusConsts::INACTIVO 
        : StatusConsts::ACTIVO;
        $resultado = ProyectoRepoAction::activaProyecto($id, $proyecto->status);

        $descripcion = "Estado de proyecto '{$proyecto->nombre}' cambiado de {$proyecto->status} a {$nuevoEstado}";

        self::agregarLog([
            'proyectoId'  => $id,
            'folio'       => $folio,
            'descripcion' => $descripcion,
        ]);

        return $resultado;
    }


    public static function obtenerLogs(int $id)
    {
        return ProyectoRepoData::obtenerLogs($id);
    }

    public static function actualizarAsignacionesUsuarios(int $proyectoId, array $usuariosNuevos, int $folio)
    {
        $usuariosActuales = ProyectoRepoData::obtenerUsuariosAsignados($proyectoId);
    
        $usuariosAgregados = array_diff($usuariosNuevos, $usuariosActuales);
        $usuariosEliminados = array_diff($usuariosActuales, $usuariosNuevos);
    
        if (!empty($usuariosAgregados) || !empty($usuariosEliminados)) {
            ProyectoRepoAction::actualizarUsuariosAsignados($proyectoId, $usuariosAgregados, $usuariosEliminados);
    
            $nombresAgregados = ProyectoRepoData::reasignarUsuarios($usuariosAgregados);
            $nombresEliminados = ProyectoRepoData::reasignarUsuarios($usuariosEliminados);
    
            $descripcion = "Actualización de asignaciones de usuarios: ";
            if ($nombresAgregados) $descripcion .= "Asignados [{$nombresAgregados}] ";
            if ($nombresEliminados) $descripcion .= "Eliminados [{$nombresEliminados}]";
    
            self::agregarLog([
                'proyectoId'  => $proyectoId,
                'folio'       => $folio,
                'descripcion' => $descripcion,
            ]);
        }
    }
    

    public static function listarUsuariosAsignados(int $proyectoId)
    {
        return ProyectoRepoData::listarUsuariosAsignados($proyectoId);
    }

    public static function obtenerPorId(int $id): ?object
    {
        return ProyectoRepoData::obtenerPorId($id);
    }

    public static function agregarLog(array $datos)
    {
        $insertLog = ProyectoBO::armarInsertLog($datos);
        return ProyectoRepoAction::crearLog($insertLog);
    }
}

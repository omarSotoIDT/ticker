<?php

namespace App\Services;

use App\RepoAction\ProyectoRepoAction;
use App\RepoData\ProyectoRepoData;


class ProyectoService
{
    public static function listar(array $filtros = [])
    {
        return ProyectoRepoData::obtenerProyectos($filtros);
    }

    public static function registrarProyecto(array $data)
    {
        $proyectoId = ProyectoRepoAction::crearProyecto($data);

        $nombreProyecto = $data['nombre'];
        ProyectoRepoAction::registrarLog(
            $proyectoId,
            'CREAR',
            "Proyecto creado: {$nombreProyecto}"
        );

        return $proyectoId;
    }

    public static function actualizarProyecto(int $id, array $data)
    {
        $proyectoActual = ProyectoRepoData::obtenerPorId($id);

        if (!$proyectoActual) {
            return false;
        }

        $anterior = (array) $proyectoActual;
        $cambios = [];

        $camposComparar = ['nombre', 'descripcion', 'cliente_id', 'status'];

        foreach ($camposComparar as $campo) {
            $valorAnterior = $anterior[$campo];
            $valorNuevo = $data[$campo];

            if ($valorAnterior !== $valorNuevo) {
                $cambios[] = "Campo: {$campo} | Antes: '{$valorAnterior}' | Después: '{$valorNuevo}'";
            }
        }

        if (!empty($cambios)) {
            $resultado = ProyectoRepoAction::actualizarProyecto($id, $data);

            $descripcion = "Proyecto actualizado:\n" . implode("\n", $cambios);

            ProyectoRepoAction::registrarLog($id, 'ACTUALIZAR', $descripcion);

            return $resultado;
        }

        return false;
    }

    public static function eliminarProyecto(int $id, string $motivo)
    {
        $proyecto = ProyectoRepoData::obtenerPorId($id);
        if (!$proyecto) {
            return false;
        }

        $resultado = ProyectoRepoAction::eliminarProyecto($id, $motivo);

        ProyectoRepoAction::registrarLog(
            $id,
            'ELIMINAR',
            "Proyecto eliminado. Motivo: {$motivo}"
        );

        return $resultado;
    }

    public static function activarProyecto(int $id)
    {
        $proyecto = ProyectoRepoData::obtenerPorId($id);
        if (!$proyecto) {
            return false;
        }

        $nuevoEstado = $proyecto->status === 'ACTIVO' ? 'INACTIVO' : 'ACTIVO';
        $resultado = ProyectoRepoAction::activaProyecto($id, $proyecto->status);

        ProyectoRepoAction::registrarLog(
            $id,
            'ESTADO',
            "Estado cambiado de {$proyecto->status} a {$nuevoEstado}"
        );

        return $resultado;
    }

    public static function obtenerLogs(int $id)
    {
        return ProyectoRepoData::obtenerLogs($id);
    }

    public static function actualizarAsignacionesUsuarios(int $proyectoId, array $usuariosNuevos)
    {
        $usuariosActuales = ProyectoRepoData::obtenerUsuariosAsignados($proyectoId);

        $usuariosAgregados = array_diff($usuariosNuevos, $usuariosActuales);
        $usuariosEliminados = array_diff($usuariosActuales, $usuariosNuevos);

        if (!empty($usuariosAgregados) || !empty($usuariosEliminados)) {
            ProyectoRepoAction::actualizarUsuariosAsignados($proyectoId, $usuariosAgregados, $usuariosEliminados);

            $nombresAgregados = ProyectoRepoData::reasignarUsuarios($usuariosAgregados);
            $nombresEliminados = ProyectoRepoData::reasignarUsuarios($usuariosEliminados);

            $descripcion = "Actualización de asignaciones: ";
            if ($nombresAgregados) $descripcion .= "Asignados [{$nombresAgregados}] ";
            if ($nombresEliminados) $descripcion .= "Eliminados [{$nombresEliminados}]";

            ProyectoRepoAction::registrarLog($proyectoId, 'ACTUALIZAR', $descripcion);
        }
    }
}

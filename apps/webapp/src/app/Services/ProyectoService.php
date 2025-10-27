<?php

namespace App\Services;

use App\RepoAction\ProyectoRepoAction;
use App\RepoData\ProyectoRepoData;
use App\BO\ProyectoBO;


class ProyectoService
{
    public static function listar(array $filtros = [])
    {
        return ProyectoRepoData::obtenerProyectos($filtros);
    }

    public static function registrarProyecto(array $data)
    {
        $proyectoId = ProyectoRepoAction::crearProyecto(ProyectoBO::datosParaInsert($data));
        $nombreProyecto = $data['nombre'];
        $clienteNombre = \App\RepoData\ClienteRepoData::obtenerNombrePorId($data['cliente_id'] ?? null);
        $descripcion = "Proyecto creado: '{$nombreProyecto}' para cliente: '{$clienteNombre}'";
        ProyectoRepoAction::registrarLog($proyectoId, $descripcion);
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
            if (array_key_exists($campo, $data)) {
                $valorAnterior = $anterior[$campo];
                $valorNuevo = $data[$campo];
                if ($valorAnterior !== $valorNuevo) {
                    if ($campo === 'cliente_id') {
                        $nombreAnterior = \App\RepoData\ClienteRepoData::obtenerNombrePorId($valorAnterior);
                        $nombreNuevo = \App\RepoData\ClienteRepoData::obtenerNombrePorId($valorNuevo);
                        $cambios[] = "Cliente cambiado de '{$nombreAnterior}' a '{$nombreNuevo}'";
                    } else {
                        $cambios[] = ucfirst($campo) . " cambiado de '{$valorAnterior}' a '{$valorNuevo}'";
                    }
                }
            }
        }
        if (!empty($cambios)) {
            $resultado = ProyectoRepoAction::actualizarProyecto($id, ProyectoBO::datosParaUpdate($data));
            $nombreProyecto = $data['nombre'] ?? $anterior['nombre'] ?? '';
            $descripcion = "Proyecto '{$nombreProyecto}' actualizado: " . implode('; ', $cambios);
            ProyectoRepoAction::registrarLog($id, $descripcion);
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
        $nombreProyecto = $proyecto->nombre ?? '';
        $resultado = ProyectoRepoAction::eliminarProyecto($id, $motivo);
        $descripcion = "Proyecto '{$nombreProyecto}' eliminado. Motivo: {$motivo}";
        ProyectoRepoAction::registrarLog($id, $descripcion);
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
        $nombreProyecto = $proyecto->nombre ?? '';
        $descripcion = "Estado de proyecto '{$nombreProyecto}' cambiado de {$proyecto->status} a {$nuevoEstado}";
        ProyectoRepoAction::registrarLog($id, $descripcion);
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

            ProyectoRepoAction::registrarLog($proyectoId, $descripcion);
        }
    }

    public static function listarUsuariosAsignados(int $proyectoId)
    {
        return ProyectoRepoData::listarUsuariosAsignados($proyectoId);
    }
}

<?php

namespace App\Coordinators;

use App\Services\UsuarioService;
use App\Services\PermisoService;
use App\Services\PerfilService;
use Illuminate\Support\Facades\DB;

class PerfilCoordinator
{
    public static function obtenerPerfiles(array $filtros = [], bool $paginate = true)
    {
        $perfiles = PerfilService::obtenerPerfiles($filtros, 10, $paginate);

        $coleccion = $paginate
            ? collect($perfiles->items())
            : collect($perfiles);

        $perfilIds = $coleccion->pluck('perfil_id')->toArray();

        $permisosPorPerfil = PermisoService::obtenerPermisosPerfiles($perfilIds);

        $coleccion->transform(function ($perfil) use ($permisosPorPerfil) {
            $perfil->permisos = $permisosPorPerfil
                ->get($perfil->perfil_id, collect())
                ->pluck('permiso_id')
                ->toArray();
            return $perfil;
        });

        return [
            'perfiles' => $paginate ? $coleccion : null,
            'perfiles_sin_paginar' => $paginate ? null : $coleccion,
            'links' => $paginate ? $perfiles->linkCollection() : null,
            'permisos' => PermisoService::obtenerPermisos(),
        ];
    }

    public static function crearPerfil(array $datos)
    {
        $permisos = $datos['permisos'];

        return DB::transaction(function () use ($datos, $permisos) {
            $perfil_id = PerfilService::crearPerfil($datos);
            PermisoService::agregarPermisos($perfil_id, $permisos);
            return $perfil_id;
        });
    }

    public static function actualizarPerfil($perfil_id, array $datos)
    {
        $permisos = $datos['permisos'];

        return DB::transaction(function () use ($perfil_id, $datos, $permisos) {
            PerfilService::actualizarPerfil($perfil_id, $datos);
            PermisoService::agregarPermisos($perfil_id, $permisos);
        });
    }

    public static function eliminarPerfil($perfil_id)
    {
        $usuariosConPerfil = UsuarioService::listar(
            ['perfil_id' => $perfil_id],
            'usuarioId'
        );

        if (count($usuariosConPerfil) > 0) {
            throw new \Exception("No se puede eliminar el perfil porque está asignado a " . count($usuariosConPerfil) . " usuario(s).");
        }

        return PerfilService::eliminarPerfil($perfil_id);
    }
}
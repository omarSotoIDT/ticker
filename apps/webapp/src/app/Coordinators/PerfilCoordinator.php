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

        if ($paginate && $perfiles instanceof \Illuminate\Pagination\LengthAwarePaginator) {
            $perfilIds = $perfiles->pluck('perfil_id')->toArray();
            $permisosPorPerfil = PermisoService::obtenerPermisosPerfiles($perfilIds);

            $perfiles->getCollection()->transform(function ($perfil) use ($permisosPorPerfil) {
                $perfil->permisos = $permisosPorPerfil
                    ->get($perfil->perfil_id, collect())
                    ->pluck('permiso_id')
                    ->toArray();
                return $perfil;
            });

            return [
                'perfiles' => $perfiles->items(),
                'perfiles_sin_paginar' => null,
                'links' => $perfiles->linkCollection(),
                'permisos' => PermisoService::obtenerPermisos(),
            ];
        } else {
            if (!empty($perfiles) && is_object($perfiles[0])) {
                $perfilIds = array_map(function ($p) {
                    return $p->perfil_id;
                }, $perfiles);
            } else {
                $perfilIds = array_column($perfiles, 'perfil_id');
            }

            $permisosPorPerfil = PermisoService::obtenerPermisosPerfiles($perfilIds);

            $perfilesConPermisos = array_map(function ($perfil) use ($permisosPorPerfil) {
                if (is_object($perfil)) {
                    $perfil->permisos = $permisosPorPerfil
                        ->get($perfil->perfil_id, collect())
                        ->pluck('permiso_id')
                        ->toArray();
                    return $perfil;
                } else {
                    $perfil['permisos'] = $permisosPorPerfil
                        ->get($perfil['perfil_id'], collect())
                        ->pluck('permiso_id')
                        ->toArray();
                    return $perfil;
                }
            }, $perfiles);

            return [
                'perfiles' => null,
                'perfiles_sin_paginar' => $perfilesConPermisos,
                'links' => null,
                'permisos' => PermisoService::obtenerPermisos(),
            ];
        }
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
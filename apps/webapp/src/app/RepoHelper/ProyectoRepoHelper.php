<?php

namespace App\RepoHelper;

use Illuminate\Database\Query\Builder;

class ProyectoRepoHelper
{
    public static function aplicarFiltros(Builder $query, array $filters): Builder
    {
        if (!empty($filters['status'])) {
            $query->where('p.status', $filters['status']);
        }

        if (!empty($filters['nombre'])) {
            $query->where('nombre', 'like', '%' . $filters['nombre'] . '%');
        }

        if (!empty($filters['cliente_id'])) {
            $query->where('cliente_id', $filters['cliente_id']);
        }

        if (!empty($filters['busqueda'])) {
            $busqueda = $filters['busqueda'];
            $query->where(function ($q) use ($busqueda) {
                $q->where('nombre', 'like', "%$busqueda%");
            });
        }

        return $query;
    }
}

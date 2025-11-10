<?php

namespace App\RepoHelper;

use Illuminate\Database\Query\Builder;

class ProyectoRepoHelper
{
    public static function aplicarFiltros(Builder $query, array $filters): Builder
    {
        if (!empty($filters['status'])) {
            $estados = is_array($filters['status']) ? $filters['status'] : [$filters['status']];
            $query->whereIn('p.status', $estados);
        }

        if (!empty($filters['cliente_id'])) {
            $clientes = is_array($filters['cliente_id']) ? $filters['cliente_id'] : [$filters['cliente_id']];
            $query->whereIn('cliente_id', $clientes);
        }

        if (!empty($filters['nombre'])) {
            $query->where('p.nombre', 'like', '%' . $filters['nombre'] . '%');
        }
        
        if (!empty($filters['busqueda'])) {
            $busqueda = $filters['busqueda'];
            $query->where(function ($q) use ($busqueda) {
                $q->where('p.nombre', 'like', "%$busqueda%")
                  ->orWhere('c.nombre', 'like', "%$busqueda%");
            });
        }

        return $query;
    }
}

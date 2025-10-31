<?php
namespace App\RepoHelper;

use Illuminate\Database\Query\Builder;

class ClienteRepoHelper
{
    public static function aplicarFiltros(Builder $query, array $filters): Builder
    {
        if (!empty($filters['status'])) {
            $estados = is_array($filters['status']) ? $filters['status'] : [$filters['status']];
            $query->whereIn('status', $estados);
        }

        if (!empty($filters['nombre'])) {
            $query->where('nombre', 'like', '%' . $filters['nombre'] . '%');
        }

        if (!empty($filters['email'])) {
            $query->where('email', 'like', '%' . $filters['email'] . '%');
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


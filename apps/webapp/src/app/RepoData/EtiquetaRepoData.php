<?php

namespace App\RepoData;

use App\RH\EtiquetaRH;
use Illuminate\Support\Facades\DB;

class EtiquetaRepoData
{
    public static function listar($filtros, $columnas, $orden, $limit, $offset) {
        $query = DB::table('etiquetas as e');

        EtiquetaRH::agregarColumnas($query, $columnas);
        EtiquetaRH::agregarFiltros($query, $filtros);
        EtiquetaRH::agregarOrden($query, $orden);

        if (isset($limit)) {
            $query->limit($limit);
        }
        if (isset($offset)) {
            $query->offset($offset);
        }
        return $query->get()->toArray();
    }
}

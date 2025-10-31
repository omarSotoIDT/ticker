<?php

namespace App\Services;

use App\RepoData\EtiquetaRepoData;

class EtiquetaService
{
    public static function listar($filtros = [], $columnas = '', $orden = [], $limit = null, $offset = null) {
        return EtiquetaRepoData::listar($filtros, $columnas, $orden, $limit, $offset);
    }
}

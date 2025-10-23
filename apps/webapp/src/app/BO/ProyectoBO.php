<?php

namespace App\BO;

class ProyectoBO
{
    public static function prepararDatos(array $data): array
    {
        return [
            'cliente_id'    => $data['cliente_id'],
            'nombre'        => $data['nombre'],
            'descripcion'   => $data['descripcion'] ,
            'status'        => $data['status'] ?? 'ACTIVO',
        ];
    }
}

<?php

namespace App\BO;

class ClienteBO
{
    public static function prepararDatos(array $data): array
    {
        return [
            'nombre'       => $data['nombre'],
            'descripcion' => $data['descripcion'],
            'contacto'     => $data['contacto'],
            'email'        => $data['email'],
            'status'      => $data['status'] ?? 'ACTIVO',
        ];
    }
}

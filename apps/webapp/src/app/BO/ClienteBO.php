<?php

namespace App\BO;

use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ClienteBO
{
    public static function armarInsert(array $data): array
    {
        return [
            'nombre' => $data['nombre'],
            'descripcion' => $data['descripcion'],
            'contacto' => $data['contacto'],
            'email' => $data['email'],
            'status' => $data['status'] ?? 'ACTIVO',
            'registro_autor_id' => Auth::id(),
            'registro_fecha'    => Carbon::now(),
        ];
    }

    public static function armarUpdate(array $data): array
    {
        return [
            'nombre' => $data['nombre'],
            'descripcion' => $data['descripcion'],
            'contacto' => $data['contacto'],
            'email' => $data['email'],
            'actualizacion_autor_id' => Auth::id(),
            'actualizacion_fecha'   => Carbon::now(),
        ];
    }
}

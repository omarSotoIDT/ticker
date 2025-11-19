<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class LogService
{
    public static function armarDescripcion($datosAnteriores, $datosNuevos)
    {        
        $cambios = [];

        foreach ($datosNuevos as $campo => $valorNuevo) {
            if (array_key_exists($campo, $datosAnteriores)) {
                $valorAnterior = $datosAnteriores[$campo];
                if ($valorAnterior != $valorNuevo) {
                    if ($campo === 'descripcion') {
                        $cambios[] = "Descripción cambiada de '{$valorAnterior}' a '{$valorNuevo}'";
                    } else {
                        $cambios[] = ucfirst($campo) . " cambiado de '{$valorAnterior}' a '{$valorNuevo}'";
                    }
                }
            }
        }
        if (!empty($cambios)) {
            return "Actualización de ticket:\n" . implode("\n", $cambios);
        } else {
            return;
        }
    }
}
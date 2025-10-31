<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Throwable;

class Exceptions
{
    /**
     * Maneja las excepciones y devuelve una respuesta JSON estandarizada.
     *
     * @param Throwable $e
     * @param string $mensajeUsuario Mensaje que verá el usuario
     * @param string|null $contexto Función o contexto donde ocurrió
     * @return \Illuminate\Http\JsonResponse
     */
    public static function handleException(Throwable $e, string $mensajeUsuario, ?string $contexto = null)
    {
        $contextoTexto = $contexto ? " ({$contexto})" : '';

        // Log detallado de la excepción
        Log::error("Error en controlador{$contextoTexto}: " . $e->getMessage(), [
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString(),
            'request' => request()->all(),
        ]);

        // Devuelve una respuesta JSON para la API
        if (request()->wantsJson()) {
            return response()->json([
                'error' => $mensajeUsuario
            ], 500);
        }

        // Si no es una petición JSON, puedes redirigir o mostrar una vista de error
        abort(500, $mensajeUsuario);
    }
}

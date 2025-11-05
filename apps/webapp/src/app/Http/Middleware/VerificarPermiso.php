<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VerificarPermiso
{
    public function handle(Request $request, Closure $next, ...$permisos)
    {
        $user = Auth::user();

        if (!$user) {
            return $this->denegar($request);
        }

        foreach ($permisos as $permiso) {
            if (tienePermiso($user->usuario_id, $permiso)) {
                return $next($request);
            }
        }

        return $this->denegar($request, $permisos[0] ?? 'desconocido');
    }

    private function denegar(Request $request, ?string $permiso = null)
    {
        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
                'error' => 'Acceso denegado',
                'mensaje' => 'No tienes permiso para realizar esta acción.',
                'permiso_requerido' => $permiso
            ], 403);
        }

        return redirect()->back()->with('error', 'No tienes permisos para realizar esta acción.');
    }
}

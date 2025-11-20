<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Services\PermisoService;

class PermisoMiddleware
{
    
    protected array $mapaPermisos = [
        'perfiles.gestor'        => 'ver_perfiles',
        'perfiles.listarRest'    => 'ver_perfiles',
        'perfiles.agregarRest'   => 'crear_perfiles',
        'perfiles.editarRest'    => 'editar_perfiles',
        'perfiles.eliminarRest'  => 'eliminar_perfiles',

        'usuarios.gestor'        => 'ver_usuarios',
        'usuarios.listarRest'    => 'ver_usuarios',
        'usuarios.agregarRest'   => 'crear_usuarios',
        'usuarios.editarRest'    => 'editar_usuarios',
        'usuarios.eliminarRest'  => 'eliminar_usuarios',
        'usuarios.activarRest'   => 'activar_usuarios',

        'proyectos.gestor'       => 'ver_proyectos',
        'proyectos.listado'      => 'ver_proyectos',
        'proyectos.registrar'    => 'crear_proyectos',
        'proyectos.actualizar'   => 'editar_proyectos',
        'proyectos.eliminar'     => 'eliminar_proyectos',
        'proyectos.activar'      => 'cambiar_status_proyectos',
        'proyectos.logs'         => 'ver_logs_proyectos',
        'proyectos.usuarios'     => 'ver_usuarios_proyectos',

        'clientes.gestor'        => 'ver_clientes',
        'clientes.listado'       => 'ver_clientes',
        'clientes.registro'      => 'crear_clientes',
        'clientes.actualizacion' => 'editar_clientes',
        'clientes.eliminacion'   => 'eliminar_clientes',
        'clientes.cambio'        => 'cambiar_status_clientes',

        'tickets.gestor'             => 'ver_tickets',
        'tickets.listarRest'         => 'ver_tickets',
        'tickets.obtenerRest'        => 'ver_tickets',
        'tickets.agregarRest'        => 'crear_tickets',
        'tickets.editarRest'         => 'editar_tickets',
        'tickets.editarStatusRest'   => 'cambiar_status_tickets',
        'tickets.editarPrioridadRest'=> 'cambiar_prioridad_tickets',
        'tickets.editarAsignacionRest'=> 'asignar_tickets',
        'tickets.agregarFeedbackRest'=> 'agregar_feedback_tickets',

        'reportes.index' => 'ver_reportes',
    ];

    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if (!$user) {
            return $this->denegar($request);
        }

        if ($user->super_usuario == 1) {
            return $next($request);
        }

        $nombreRuta = Route::currentRouteName();

        if (!$nombreRuta) {
            return $this->denegar($request, 'ruta-desconocida');
        }

        $permiso = $this->mapaPermisos[$nombreRuta] ?? null;

        if (!$permiso) {
            return $next($request);
        }

        if (PermisoService::tienePermiso($user->usuario_id, $permiso)) {
            return $next($request);
        }

        return $this->denegar($request, $permiso);
    }

    private function denegar(Request $request, ?string $permiso = null)
    {
        $mensaje = $permiso
            ? "No tienes permisos para realizar la acción: $permiso."
            : "No tienes permisos para realizar esta acción.";

        if ($request->wantsJson() || $request->ajax() || $request->is('api/*')) {
            return response()->json([
                'error' => true,
                'mensaje' => $mensaje,
                'permiso_requerido' => $permiso,
            ], 403);
        }

        if (url()->previous() && url()->previous() !== url()->current()) {
            return redirect()->back()->with('error', $mensaje);
        }

        return redirect()->route('dashboard')->with('error', $mensaje);
    }
}
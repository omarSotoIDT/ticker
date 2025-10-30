<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Coordinators\PerfilesCoordinator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PerfilesController extends Controller
{
    public function index()
    {
        try {
            $busqueda = request('busqueda', ''); 
            $coordinator = new PerfilesCoordinator();
            $perfiles = $coordinator->obtenerPerfiles(['busqueda' => $busqueda]);
            $permisos = $coordinator->obtenerPermisos();

            $perfilesConPermisos = $perfiles->filter(function ($perfil) {
                return $perfil->super_usuario != 1;
            })->values();

            $perfilesConPermisos = $perfilesConPermisos->map(function ($perfil) use ($permisos) {
                $permisosAsignados = DB::table('rel_perfiles_permisos')
                    ->where('perfil_id', $perfil->perfil_id)
                    ->pluck('permiso_id')
                    ->toArray();
                $perfil->permisos = $permisosAsignados;
                return $perfil;
            });

            return view('perfiles.Perfiles', compact('perfilesConPermisos', 'permisos', 'busqueda'));
        } catch (\Exception $e) {
            Log::error('Error en index: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al cargar perfiles: ' . $e->getMessage());
        }
    }

    public function guardar(Request $request)
    {
        try {
            $coordinator = new PerfilesCoordinator();
            $coordinator->crearPerfil($request->all());
            return redirect()->back()->with('success', 'Perfil creado exitosamente');
        } catch (\Exception $e) {
            Log::error('Error en guardar: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al crear perfil: ' . $e->getMessage());
        }
    }

    public function actualizar(Request $request, $perfil_id)
    {
        try {
            $coordinator = new PerfilesCoordinator();
            $coordinator->actualizarPerfil($perfil_id, $request->all());
            return redirect()->back()->with('success', 'Perfil actualizado exitosamente');
        } catch (\Exception $e) {
            Log::error('Error en actualizar: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al actualizar perfil: ' . $e->getMessage());
        }
    }

    public function eliminar($perfil_id)
    {
        try {
            $coordinator = new PerfilesCoordinator();
            $coordinator->eliminarPerfil($perfil_id);
            return redirect()->back()->with('success', 'Perfil eliminado exitosamente');
        } catch (\Exception $e) {
            Log::error('Error en eliminar: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al eliminar perfil: ' . $e->getMessage());
        }
    }
}
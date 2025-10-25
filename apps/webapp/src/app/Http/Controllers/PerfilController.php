<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Coordinators\PerfilCoordinator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PerfilController extends Controller
{
    public function index()
    {
        try {
            $busqueda = request('busqueda', ''); 

            $perfiles = PerfilCoordinator::obtenerPerfiles(['busqueda' => $busqueda]);
            $permisos = PerfilCoordinator::obtenerPermisos();

            $perfilesConPermisos = $perfiles->values()->map(function ($perfil) use ($permisos) {
                $permisosAsignados = DB::table('rel_perfiles_permisos')
                    ->where('perfil_id', $perfil->perfil_id)
                    ->pluck('permiso_id')
                    ->toArray();
                $perfil->permisos = $permisosAsignados;
                return $perfil;
            });

            return view('perfiles.perfiles', compact('perfilesConPermisos', 'permisos', 'busqueda'));
        } catch (\Exception $e) {
            Log::error('Error en index: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al cargar perfiles: ' . $e->getMessage());
        }
    }

    public function guardar(Request $request)
    {
        try {
            PerfilCoordinator::crearPerfil($request->all());
            return redirect()->back()->with('success', 'Perfil creado exitosamente');
        } catch (\Exception $e) {
            Log::error('Error en guardar: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al crear perfil: ' . $e->getMessage());
        }
    }

    public function actualizar(Request $request, $perfil_id)
    {
        try {
            PerfilCoordinator::actualizarPerfil($perfil_id, $request->all());
            return redirect()->back()->with('success', 'Perfil actualizado exitosamente');
        } catch (\Exception $e) {
            Log::error('Error en actualizar: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al actualizar perfil: ' . $e->getMessage());
        }
    }

    public function eliminar($perfil_id)
    {
        try {
            PerfilCoordinator::eliminarPerfil($perfil_id);
            return redirect()->back()->with('success', 'Perfil eliminado exitosamente');
        } catch (\Exception $e) {
            Log::error('Error en eliminar: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al eliminar perfil: ' . $e->getMessage());
        }
    }
}

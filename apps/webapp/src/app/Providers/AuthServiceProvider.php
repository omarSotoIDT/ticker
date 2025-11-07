<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;
use App\RH\PermisoRH; 

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [];

    public function boot(): void
    {
        $this->registerPolicies();

        Gate::before(function ($user, $ability) {
            $perfil = DB::table('rel_usuarios_perfiles')
                ->join('sys_perfiles', 'rel_usuarios_perfiles.perfil_id', '=', 'sys_perfiles.perfil_id')
                ->where('rel_usuarios_perfiles.usuario_id', $user->usuario_id)
                ->first();

            if ($perfil && (int) $perfil->super_usuario === 1) {
                return true;
            }

            return null;
        });

        $permisos = DB::table('sys_permisos')->pluck('codigo');

        foreach ($permisos as $codigo) {
            if (! Gate::has($codigo)) {
                Gate::define($codigo, function ($user) use ($codigo) {
                    return PermisoRH::tienePermiso($user->usuario_id, $codigo); 
                });
            }
        }
    }
}

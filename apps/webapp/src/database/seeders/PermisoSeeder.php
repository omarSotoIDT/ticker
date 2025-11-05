<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermisoSeeder extends Seeder
{
    public function run()
    {
        $permisos = [

            // Permisos para el módulo de Perfiles
            ['codigo' => 'ver_perfiles', 'titulo' => 'Ver Perfiles', 'orden' => 1, 'descripcion' => 'Permite ver la lista de perfiles del sistema'],
            ['codigo' => 'crear_perfiles', 'titulo' => 'Crear Perfiles', 'orden' => 2, 'descripcion' => 'Permite crear nuevos perfiles'],
            ['codigo' => 'editar_perfiles', 'titulo' => 'Editar Perfiles', 'orden' => 3, 'descripcion' => 'Permite editar perfiles existentes'],
            ['codigo' => 'eliminar_perfiles', 'titulo' => 'Eliminar Perfiles', 'orden' => 4, 'descripcion' => 'Permite eliminar perfiles'],

            // Permisos para el módulo de Usuarios
            ['codigo' => 'ver_usuarios', 'titulo' => 'Ver Usuarios', 'orden' => 5, 'descripcion' => 'Permite ver la lista de usuarios'],
            ['codigo' => 'crear_usuarios', 'titulo' => 'Crear Usuarios', 'orden' => 6, 'descripcion' => 'Permite crear nuevos usuarios'],
            ['codigo' => 'editar_usuarios', 'titulo' => 'Editar Usuarios', 'orden' => 7, 'descripcion' => 'Permite editar usuarios existentes'],
            ['codigo' => 'eliminar_usuarios', 'titulo' => 'Eliminar Usuarios', 'orden' => 8, 'descripcion' => 'Permite eliminar usuarios'],
            ['codigo' => 'activar_usuarios', 'titulo' => 'Activar/Desactivar Usuarios', 'orden' => 9, 'descripcion' => 'Permite activar o desactivar usuarios'],

            // Permisos para el módulo de Proyectos
            ['codigo' => 'ver_proyectos', 'titulo' => 'Ver Proyectos', 'orden' => 10, 'descripcion' => 'Permite ver la lista de proyectos'],
            ['codigo' => 'crear_proyectos', 'titulo' => 'Crear Proyectos', 'orden' => 11, 'descripcion' => 'Permite crear nuevos proyectos'],
            ['codigo' => 'editar_proyectos', 'titulo' => 'Editar Proyectos', 'orden' => 12, 'descripcion' => 'Permite editar proyectos existentes'],
            ['codigo' => 'eliminar_proyectos', 'titulo' => 'Eliminar Proyectos', 'orden' => 13, 'descripcion' => 'Permite eliminar proyectos'],
            ['codigo' => 'cambiar_status_proyectos', 'titulo' => 'Cambiar Status Proyectos', 'orden' => 14, 'descripcion' => 'Permite cambiar el estado de un proyecto'],
            ['codigo' => 'ver_logs_proyectos', 'titulo' => 'Ver Logs Proyectos', 'orden' => 15, 'descripcion' => 'Permite ver el historial de cambios de un proyecto'],
            ['codigo' => 'ver_usuarios_proyectos', 'titulo' => 'Ver Usuarios en Proyecto', 'orden' => 16, 'descripcion' => 'Permite ver usuarios asignados a un proyecto'],

            // Permisos para el módulo de Clientes
            ['codigo' => 'ver_clientes', 'titulo' => 'Ver Clientes', 'orden' => 17, 'descripcion' => 'Permite ver la lista de clientes'],
            ['codigo' => 'crear_clientes', 'titulo' => 'Crear Clientes', 'orden' => 18, 'descripcion' => 'Permite crear nuevos clientes'],
            ['codigo' => 'editar_clientes', 'titulo' => 'Editar Clientes', 'orden' => 19, 'descripcion' => 'Permite editar clientes existentes'],
            ['codigo' => 'eliminar_clientes', 'titulo' => 'Eliminar Clientes', 'orden' => 20, 'descripcion' => 'Permite eliminar clientes'],
            ['codigo' => 'cambiar_status_clientes', 'titulo' => 'Cambiar Status Clientes', 'orden' => 21, 'descripcion' => 'Permite cambiar el estado de un cliente'],

            // Permisos para el módulo de Tickets
            ['codigo' => 'ver_tickets', 'titulo' => 'Ver Tickets', 'orden' => 22, 'descripcion' => 'Permite ver la lista de tickets'],
            ['codigo' => 'crear_tickets', 'titulo' => 'Crear Tickets', 'orden' => 23, 'descripcion' => 'Permite crear nuevos tickets'],
            ['codigo' => 'editar_tickets', 'titulo' => 'Editar Tickets', 'orden' => 24, 'descripcion' => 'Permite editar tickets existentes'],
            ['codigo' => 'cambiar_status_tickets', 'titulo' => 'Cambiar Status Tickets', 'orden' => 25, 'descripcion' => 'Permite cambiar el estado de un ticket'],
            ['codigo' => 'cambiar_prioridad_tickets', 'titulo' => 'Cambiar Prioridad', 'orden' => 26, 'descripcion' => 'Permite cambiar la prioridad de un ticket'],
            ['codigo' => 'asignar_tickets', 'titulo' => 'Asignar Tickets', 'orden' => 27, 'descripcion' => 'Permite asignar tickets a usuarios'],
            ['codigo' => 'ver_feedback_tickets', 'titulo' => 'Ver Feedback', 'orden' => 28, 'descripcion' => 'Permite ver comentarios en tickets'],
            ['codigo' => 'agregar_feedback_tickets', 'titulo' => 'Agregar Feedback', 'orden' => 29, 'descripcion' => 'Permite agregar comentarios en tickets'],
        ];

        foreach ($permisos as $p) {
            DB::table('sys_permisos')->updateOrInsert(
                ['codigo' => $p['codigo']],
                $p
            );
        }
    }
}
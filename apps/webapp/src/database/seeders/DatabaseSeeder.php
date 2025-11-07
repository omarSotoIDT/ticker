<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */

        public function run(): void
        {
            DB::table('sys_usuarios')->insert([
                'usuario' => 'Pablo',
                'email' => 'pablo@example.com',
                'password' => Hash::make('12345678'),
                'ultimo_acceso_fecha' => null,
                'status' => 'ACTIVO',
                'super_usuario' => 1, 
                'motivo_eliminacion' => null,
                'registro_autor_id' => 1, 
                'registro_fecha' => now(),
                'actualizacion_autor_id' => null,
                'actualizacion_fecha' => null,
            ]);
        }
    }


<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rel_perfiles_permisos', function (Blueprint $table) {
            $table->id('rel_permiso_perfil_id');
            $table->unsignedBigInteger('perfil_id');
            $table->unsignedBigInteger('permiso_id');
            $table->unsignedBigInteger('registro_autor_id');
            $table->timestamp('registro_fecha')->useCurrent();

            //Claves foráneas
            $table->foreign('perfil_id')
                  ->references('perfil_id')
                  ->on('sys_perfiles')
                  ->onDelete('cascade');

            $table->foreign('permiso_id')
                  ->references('permiso_id')
                  ->on('sys_permisos')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rel_perfiles_permisos');
    }
};

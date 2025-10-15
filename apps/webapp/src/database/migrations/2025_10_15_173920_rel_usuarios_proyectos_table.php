<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rel_usuarios_proyectos', function (Blueprint $table) {
            $table->id('rel_usuario_proyecto_id');
            $table->unsignedBigInteger('usuario_id');
            $table->unsignedBigInteger('proyecto_id');

            $table->enum('status',['activo','eliminado'])->default('activo');
            
            $table->unsignedBigInteger('registro_autor_id');
            $table->timestamp('regristro_fecha');
            $table->unsignedBigInteger('actualizacion_autor_id')->nullable();
            $table->timestamp('actualizacion_fecha')->nullable();
            $table->foreign('usuario_id')->references('usuario_id')->on('sys_usuarios');
            $table->foreign('proyecto_id')->references('proyecto_id')->on('proyectos');

            $table->unique(['usuario_id', 'proyecto_id']); // evita duplicados
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rel_usuarios_proyectos');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sys_perfiles', function (Blueprint $table) {
            $table->unsignedBigInteger('perfil_id')->autoIncrement()->primary();
            $table->text('clave');
            $table->text('nombre');
            $table->text('descripcion');
            $table->enum('status', ['ACTIVO','ELIMINADO'])->comment('ACTIVO, ELIMINADO');
            $table->tinyInteger('super_usuario');
            $table->unsignedBigInteger('registro_autor_id');
            $table->timestamp('registro_fecha');
            $table->unsignedBigInteger('actualizacion_autor_id')->nullable();
            $table->timestamp('actualizacion_fecha')->nullable();
        });

        Schema::create('rel_usuarios_perfiles', function (Blueprint $table){
            $table->unsignedBigInteger('rel_usuario_perfil_id')->autoIncrement()->primary();
            $table->unsignedBigInteger('perfil_id');
            $table->unsignedBigInteger('usuario_id');
            $table->unsignedBigInteger('registro_autor_id');
            $table->timestamp('registro_fecha');
            
            $table->foreign('perfil_id')->references('perfil_id')->on('sys_perfiles');
            $table->foreign('usuario_id')->references('usuario_id')->on('sys_usuarios');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sys_perfiles');
        Schema::dropIfExists('rel_usuarios_perfiles');
    }
};

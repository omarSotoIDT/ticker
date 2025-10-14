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
            $table->string('clave', 20);
            $table->string('titulo', 45);
            $table->string('description', 250)->nullable();
            $table->string('status', 255)->nullable();
            $table->tinyInteger('acceso_aplicacion')->nullable();
            $table->tinyInteger('super_usuario');
            $table->unsignedBigInteger('registro_autor_id');
            $table->timestamp('registro_fecha');
            $table->unsignedBigInteger('actualizacion_autor_id');
            $table->timestamp('actualizacion_fecha')->nullable();
        });

        Schema::create('rel_usuarios_perfiles', function (Blueprint $table){
            $table->unsignedBigInteger('rel_usuario_perfil_id')->autoIncrement()->primary();
            $table->unsignedBigInteger('perfil_id');
            $table->foreign('perfil_id')->references('perfil_id')->on('sys_perfiles')->onDelete('cascade');
            $table->unsignedBigInteger('usuario_id');
            $table->foreign('usuario_id')->references('usuario_id')->on('sys_usuarios')->onDelete('cascade');
            $table->string('status', 255)->default('ACTIVO');
            $table->unsignedBigInteger('registro_autor_id');
            $table->timestamp('registro_fecha');
            $table->unsignedBigInteger('actualizacion_autor_id');
            $table->timestamp('actualizacion_fecha')->nullable();
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

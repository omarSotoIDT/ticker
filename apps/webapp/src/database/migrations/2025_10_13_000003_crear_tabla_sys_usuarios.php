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
        Schema::create('sys_usuarios', function (Blueprint $table) {
            $table->id('usuario_id');
            $table->text('usuario');
            $table->text('email');
            $table->text('password');
            $table->timestamp('ultimo_acceso_fecha')->nullable();
            $table->enum('status', ['ACTIVO','ELIMINADO'])->comment('Columna que posee las opciones de ACTIVO y ELIMINADO');
            $table->tinyInteger('super_usuario')->default(0);
            $table->text('motivo_eliminacion')->nullable();
            $table->unsignedBigInteger('registro_autor_id');
            $table->timestamp('registro_fecha');
            $table->unsignedBigInteger('actualizacion_autor_id')->nullable();
            $table->timestamp('actualizacion_fecha')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sys_usuarios');
    }
};

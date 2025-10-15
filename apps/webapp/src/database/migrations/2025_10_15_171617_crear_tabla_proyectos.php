<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('proyectos', function (Blueprint $table) {
            $table->unsignedBigInteger('proyecto_id')->autoIncrement()->primary();
            $table->unsignedBigInteger('cliente_id');
            $table->text('nombre');
            $table->text('descripcion');
            $table->enum('status', ['ACTIVO', 'INACTIVO', 'ELIMINADO'])->default('ACTIVO')->comment('ACTIVO, INACTIVO, ELIMINADO');
            $table->text('motivo_eliminacion');
            $table->unsignedBigInteger('registro_autor_id');
            $table->timestamp('registro_fecha');
            $table->unsignedBigInteger('actualizacion_autor_id')->nullable();
            $table->timestamp('actualizacion_fecha')->nullable();

            $table->foreign('cliente_id')->references('clientes')->on('cliente_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proyectos');
    }
};

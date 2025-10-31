<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {        
        Schema::create('tickets', function (Blueprint $table){
            $table->unsignedBigInteger('ticket_id')->autoIncrement()->primary();
            $table->unsignedBigInteger('cliente_id');
            $table->unsignedBigInteger('proyecto_id');
            $table->unsignedBigInteger('etiqueta_id');
            $table->unsignedBigInteger('usuario_asignado_id');
            $table->unsignedBigInteger('folio');
            $table->text('serie_folio');
            $table->text('titulo');
            $table->text('descripcion');
            $table->enum('prioridad', ['Baja','Media','Alta','Urgente'])->comment('Baja','Media','Alta','Urgente');
            $table->text('status');
            $table->unsignedBigInteger('registro_autor_id');
            $table->timestamp('registro_fecha');
            $table->unsignedBigInteger('actualizacion_autor_id')->nullable();
            $table->timestamp('actualizacion_fecha')->nullable();

            $table->foreign('cliente_id')->references('cliente_id')->on('clientes');
            $table->foreign('proyecto_id')->references('proyecto_id')->on('proyectos');
            $table->foreign('etiqueta_id')->references('etiqueta_id')->on('etiquetas');
            $table->foreign('usuario_asignado_id')->references('usuario_id')->on('sys_usuarios');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
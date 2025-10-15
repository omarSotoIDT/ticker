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
            $table->foreignId('cliente_id')->constrained('clientes', 'cliente_id');
            $table->text('nombre');
            $table->text('descripcion');
            $table->enum('status', ['activo', 'inactivo', 'eliminado'])->default('activo')->comment('activo, inactivo, eliminado');
            $table->text('motivo_eliminacion');
            $table->unsignedBigInteger('registro_autor_id');
            $table->timestamp('registro_fecha');
            $table->unsignedBigInteger('actualizacion_autor_id')->nullable();
            $table->timestamp('actualizacion_fecha')->nullable();
        });
    }

    public function down(): void
    {
        Schema::create('tickets', function (Blueprint $table){
            $table->unsignedBigInteger('ticket_id')->autoIncrement()->primary();
            $table->foreignId('cliente_id')->constrained('clientes', 'cliente_id');
            $table->foreignId('proyecto_id')->constrained('proyectos', 'proyecto_id');
            $table->foreignId('etiqueta_id')->constrained('etiquetas', 'etiqueta_id');
            $table->foreignId('usuario_asignado_id')->constrained('sys_usuarios', 'usuario_id');
            $table->unsignedBigInteger('folio');
            $table->text('titulo');
            $table->text('descripcion');
            $table->enum('prioridad', ['baja','media','alta','urgente'])->comment('baja','media','alta','urgente');
            $table->text('status');
            $table->unsignedBigInteger('registro_autor_id');
            $table->timestamp('registro_fecha');
            $table->unsignedBigInteger('actualizacion_autor_id')->nullable();
            $table->timestamp('actualizacion_fecha')->nullable();
        });
    }
};
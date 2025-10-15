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
        Schema::create('etiquetas', function (Blueprint $table) {
            $table->id('etiqueta_id');
            $table->text('titulo');
            $table->text('descripcion');
            $table->enum('status', ['ACTIVO','ELIMINADO'])->comment('Columna que posee las opciones de ACTIVO y ELIMINADO');
            $table->unsignedBigInteger('registro_autor_id');
            $table->timestamp('registro_fecha')->comment('Columna que hace referencia a la fecha en la cual se dio de alta el usuario');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};

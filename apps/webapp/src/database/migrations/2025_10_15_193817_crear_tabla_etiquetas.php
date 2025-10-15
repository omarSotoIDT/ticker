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
            $table->bigIncrements('etiqueta_id')->primary();
            $table->text('itulo');
            $table->text('descripcion');
            $table->enum('status', ['activo','eliminado'])->comment('Columna que solo permite activo o elimnado');
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

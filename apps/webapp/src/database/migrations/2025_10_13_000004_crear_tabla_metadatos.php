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
        Schema::create('configuraciones_metadatos', function (Blueprint $table) {
            $table->bigIncrements('configuracion_metadato_id')->primary();
            $table->text('clave')->comment('Clave única de identificación');
            $table->text('descripcion')->comment('Descripción del metadato');
            $table->json('valor')->comment('Valor en formato JSON');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('configuraciones_metadatos');
    }
};

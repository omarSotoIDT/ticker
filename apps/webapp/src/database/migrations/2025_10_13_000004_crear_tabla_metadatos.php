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
        Schema::create('metadatos', function (Blueprint $table) {
            $table->bigIncrements('metadato_id');
            $table->string('clave', 50)->nullable(false)->comment('Clave única de identificación');
            $table->string('descripcion', 100)->nullable(false)->comment('Descripción del metadato');
            $table->json('valor')->nullable(false)->comment('Valor en formato JSON');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('metadatos');
    }
};

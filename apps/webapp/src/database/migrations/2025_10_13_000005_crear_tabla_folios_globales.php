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
        Schema::create('folios_globales', function (Blueprint $table) {
            $table->bigIncrements('folio_global_id');
            $table->string('key', 255)->nullable(false)->comment('Clave única de identificación');
            $table->bigInteger('folio')->nullable(false)->comment('Número de folio global');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('folios_globales');
    }
};

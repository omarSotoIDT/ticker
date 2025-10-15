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
        Schema::create('sys_permisos', function (Blueprint $table) {
            $table->id('permiso_id'); // bigint unsigned, auto_increment, PK
            $table->text('codigo');
            $table->text('titulo');
            $table->unsignedBigInteger('orden');
            $table->text('descripcion');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sys_permisos');
    }
};

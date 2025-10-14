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
            $table->string('codigo', 150);
            $table->string('titulo', 75);
            $table->string('descripcion', 350);
            $table->string('seccion', 350);
            $table->decimal('orden', 5, 2);
            //$table->timestamps();
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

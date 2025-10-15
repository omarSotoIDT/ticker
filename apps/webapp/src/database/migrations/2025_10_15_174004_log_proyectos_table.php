<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('log_proyectos', function (Blueprint $table) {
            $table->id('log_proyecto_id');
            $table->unsignedBigInteger('proyecto_id');
            $table->unsignedBigInteger('usuario_id');
            $table->unsignedBigInteger('folio');
            $table->text('descripcion');
            $table->timestamp('registro_fecha');

            $table->foreign('proyecto_id')->references('proyecto_id')->on('proyectos')->onDelete('cascade');
            $table->foreign('usuario_id')->references('usuario_id')->on('sys_usuarios')->onDelete('cascade');

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('log_proyectos');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tickets_feedback', function (Blueprint $table) {
            $table->id('ticket_feedback_id');
            $table->unsignedBigInteger('ticket_id');
            $table->unsignedBigInteger('usuario_id');
            $table->unsignedBigInteger('folio');
            $table->text('comentario');
            $table->timestamp('registro_fecha');

            $table->foreign('ticket_id')->references('ticket_id')->on('tickets')->onDelete('cascade');
            $table->foreign('usuario_id')->references('usuario_id')->on('sys_usuarios')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets_feedback');
    }
};


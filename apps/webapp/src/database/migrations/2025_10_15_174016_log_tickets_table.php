<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('log_tickets', function (Blueprint $table) {
            $table->id('log_ticket_id');
            $table->unsignedBigInteger('ticket_id');
            $table->unsignedBigInteger('usuario_id');
            $table->unsignedBigInteger('folio');
            $table->text('descripcion');
            $table->timestamp('registro_fecha');

            $table->foreign('ticket_id')->references('ticket_id')->on('tickets');
            $table->foreign('usuario_id')->references('usuario_id')->on('sys_usuarios');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('log_tickets');
    }
};

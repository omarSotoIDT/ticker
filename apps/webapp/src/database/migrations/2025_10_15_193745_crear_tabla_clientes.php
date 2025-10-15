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
        Schema::create('clientes', function (Blueprint $table) {
            $table->bigIncrements('cliente_id')->primary();
            $table->text('nombre');
            $table->text('descripcion');
            $table->text('contacto')->comment('Hace referencia a un nombre que se da como contacto por parte del cliente');
            $table->text('email');
            $table->enum('status', ['activo','inactivo'])->comment('Columna que solo permite activo o inactivo');
            $table->text('motivo_eliminacion')->nullable()->comment('Columna que hace eferencia al motivo por el cual se haya eliminado el usuario');
            $table->unsignedBigInteger('registro_autor_id');
            $table->timestamp('registro_fecha')->comment('Columna que hace referencia a la fecha en la cual se dio de alta el usuario');
            $table->unsignedBigInteger('actualizacion_autor_id')->nullable();
            $table->timestamp('actualizacion_fecha')->nullable();
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

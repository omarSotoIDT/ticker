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
        Schema::create('sys_usuarios', function (Blueprint $table) {
            $table->bigIncrements('usuario_id')->unique();
            $table->text('usuario');
            $table->text('email');
            $table->text('password');
            $table->timestamp('ultimo_acceso_fecha')->nullable();
            $table->enum('status', ['activo','eliminado']);
            $table->tinyInteger('super_usuario')->default(0)->comment('Columna que sirve para indicar el usuario system');
            $table->text('motivo_eliminacion')->nullable()->comment('Columna que hace eferencia al motivo por el cual se haya eliminado el usuario');
            $table->bigInteger('registro_autor_id');
            $table->timestamp('registro_fecha')->nullable()->comment('Columna que hace referencia a la fecha en la cual se dio de alta el usuario');
            $table->bigInteger('actualizacion_autor_id')->nullable();
            $table->timestamp('actualizacion_fecha')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sys_usuarios');
    }
};

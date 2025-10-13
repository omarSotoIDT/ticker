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
            $table->bigIncrements('usuario_id');
            $table->string('usuario', 20)->nullable(false);
            $table->string('password', 128)->nullable(false);
            $table->integer('pin')->nullable();
            $table->string('nombre_corto', 100)->nullable(false);
            $table->string('email', 200)->nullable();
            $table->string('telefono', 25)->nullable();
            $table->timestamp('ultimo_acceso_fecha')->nullable();
            $table->boolean('acceso_aplicacion')->default(0);
            $table->string('status', 255)->nullable(false);
            $table->tinyInteger('super_usuario_mantenimiento')->comment('Columna que sirve para indicar el usuario de system que no se mostrará en el sistema');
            $table->tinyInteger('super_usuario')->default(0);
            $table->timestamp('registro_fecha')->nullable();
            $table->unsignedBigInteger('registro_autor_id')->nullable();
            $table->timestamp('actualizacion_fecha')->nullable();
            $table->unsignedBigInteger('actualizacion_autor_id')->nullable();
            $table->unsignedBigInteger('colaborador_id')->nullable()->unique();
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

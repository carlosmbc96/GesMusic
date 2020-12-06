<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrazasTable extends Migration
{
    public function up()
    {
        Schema::create('trazas', function (Blueprint $table) {
            $table->id()->index();  // Identificador Único de la Tabla: Trazas
            $table->string('nombreUser')->nullable();  // Nombre Usuario Traza
            $table->ipAddress('ipDireccion')->nullable();  // Dirección IP genera Traza
            $table->text('url')->nullable();  // URL de la Traza
            $table->string('usuarioAgent')->nullable();  // Agente del Usuario
            $table->text('valorAntg')->nullable();  // Valor Antiguo Traza
            $table->text('ValorNue')->nullable();  // Valor Nuevo Traza

            $table->softDeletes();  // Eliminación lógica del Registro
            $table->timestamps();  // Fecha de Creación y Actualización del Registro

            // Relaciones
            $table->foreignId('usuario_id')->references('id')->on('usuarios');
        });
    }

    public function down()
    {
        Schema::dropIfExists('trazas');
    }
}

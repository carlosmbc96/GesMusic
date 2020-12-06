<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AudiovisualInterprete extends Migration
{
    public function up()
    {
        Schema::create('audiovisual_interprete', function (Blueprint $table) {
            $table->id()->index();  // Identificador Único de la Tabla: Parámetros
            $table->string('rolInterp');  // Rol del Interprete

            $table->softDeletes();  // Eliminación lógica del Registro
            $table->timestamps();  // Fecha de Creación y Actualización del Registro

            // Relaciones
            $table->foreignId('audiovisual_id')->references('id')->on('audiovisuales');
            $table->foreignId('interprete_id')->references('id')->on('interpretes');
        });
    }

    public function down()
    {
        Schema::dropIfExists('audiovisual_interprete');
    }
}

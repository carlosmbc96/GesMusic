<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRealizadoresTable extends Migration
{
    public function up()
    {
        Schema::create('realizadores', function (Blueprint $table) {
            // Migración del Modelo Realizadores
            $table->id()->index();  // Identificador Único de la Tabla: Realizadores
            $table->string('nombreApellidosRealiz');  // Nombre y Apellidos del Realizador
            $table->string('sexoRealiz');  // Sexo Realizador *nom
            $table->text('descripEspRealiz')->nullable();  // Descripción en Español del Realizador

            $table->softDeletes();  // Eliminación lógica del Registro
            $table->timestamps();  // Fecha de Creación y Actualización del Registro

            // Relaciones
            $table->foreignId('audiovisual_id')->references('id')->on('audiovisuales');
        });
    }

    public function down()
    {
        Schema::dropIfExists('realizadores');
    }
}

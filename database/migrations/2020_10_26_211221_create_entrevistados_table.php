<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEntrevistadosTable extends Migration
{
    public function up()
    {
        Schema::create('entrevistados', function (Blueprint $table) {
            // Migración del Modelo Entrevistados
            $table->id()->index();  // Identificador Único de la Tabla: Entrevistados
            $table->string('nombreApellidosEntrv');  // Nombre y Apellidos del Entrevistado
            $table->string('sexoEntrv');  // Sexo del Entrevistado
            $table->text('descripEspEntrv')->nullable();  // Descripción en Español del Entrevistado

            $table->softDeletes();  // Eliminación lógica del Registro
            $table->timestamps();  // Fecha de Creación y Actualización del Registro

            // Relaciones
            $table->foreignId('audiovisual_id')->references('id')->on('audiovisuales');
        });
    }

    public function down()
    {
        Schema::dropIfExists('entrevistados');
    }
}

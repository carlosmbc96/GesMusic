<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTerminosTable extends Migration
{
    public function up()
    {
        Schema::create('terminos', function (Blueprint $table) {
            $table->id()->index();  // Identificador Único de la Tabla: Terminos
            $table->string('acronimoTer');  // Acrónimo Término
            $table->string('nombreTer');  // Nombre Término
            $table->text('descripTer')->nullable();  // Descripción Término

            $table->softDeletes();  // Eliminación lógica del Registro
            $table->timestamps();  // Fecha de Creación y Actualización del Registro

            // Relaciones
            $table->foreignId('vocabulario_id')->references('id')->on('vocabularios');
        });
    }

    public function down()
    {
        Schema::dropIfExists('terminos');
    }
}

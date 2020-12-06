<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVocabulariosTable extends Migration
{
    public function up()
    {
        Schema::create('vocabularios', function (Blueprint $table) {
            $table->id()->index();  // Identificador Único de la Tabla: Vocabularios
            $table->string('acronimoVoc')->nullable();  // Acrónimo Vocabulario
            $table->string('nombreVoc');  // Nombre Vocabulario
            $table->text('descripVoc')->nullable();  // Descripción Vocabulario

            $table->softDeletes();  // Eliminación lógica del Registro
            $table->timestamps();  // Fecha de Creación y Actualización del Registro
        });
    }

    public function down()
    {
        Schema::dropIfExists('vocabularios');
    }
}

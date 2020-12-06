<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateParametrosTable extends Migration
{
    public function up()
    {
        Schema::create('parametros', function (Blueprint $table) {
            $table->id()->index();  // Identificador Único de la Tabla: Parámetros
            $table->string('nombreParmt');  // Nombre Parámetros
            $table->string('acronimoParmt')->nullable();  // Acrónimo Parámetros
            $table->string('valorParmt');  // Valor Parámetros
            $table->text('descripParmt')->nullable();  // Descripción Parámetros

            $table->softDeletes();  // Eliminación lógica del Registro
            $table->timestamps();  // Fecha de Creación y Actualización del Registro
        });
    }

    public function down()
    {
        Schema::dropIfExists('parametros');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTemasTable extends Migration
{
    public function up()
    {
        Schema::create('temas', function (Blueprint $table) {
            $table->id()->index();  // Identificador Único de la Tabla: Temas
            $table->string('tituloTem');  // Título Tema
            $table->boolean('catalDigitalTem')->nullable();  // ¿Estará Tema en el Catálogo Digital?
            $table->string('sociedadGestionTem')->nullable();  // Sociedad de Gestión Tema *nom
            $table->text('descripTem')->nullable();  // Descripción Tema

            $table->softDeletes();  // Eliminación lógica del Registro
            $table->timestamps();  // Fecha de Creación y Actualización del Registro

            // Relaciones
            $table->foreignId('track_id')->references('id')->on('tracks');
        });
    }

    public function down()
    {
        Schema::dropIfExists('temas');
    }
}

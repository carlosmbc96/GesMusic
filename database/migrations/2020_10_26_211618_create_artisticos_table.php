<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArtisticosTable extends Migration
{
    public function up()
    {
        Schema::create('artisticos', function (Blueprint $table) {
            $table->id()->index();  // Identificador Único de la Tabla: Artisticos
            $table->string('NombreArts');  // Nombre Artistico
            $table->string('actualNombreArts')->nullable();  // ¿Es el Actual Nombre Artístico?
            $table->text('descripNombreArts')->nullable();  // Descripción Nombre Artístico

            $table->softDeletes();  // Eliminación lógica del Registro
            $table->timestamps();  // Fecha de Creación y Actualización del Registro

            // Relaciones
            $table->foreignId('interprete_id')->references('id')->on('interpretes');
        });
    }

    public function down()
    {
        Schema::dropIfExists('artisticos');
    }
}

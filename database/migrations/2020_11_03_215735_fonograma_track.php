<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FonogramaTrack extends Migration
{
    public function up()
    {
        Schema::create('fonograma_track', function (Blueprint $table) {
            $table->id()->index();  // Identificador Único de la Tabla: Parámetros

            $table->softDeletes();  // Eliminación lógica del Registro
            $table->timestamps();  // Fecha de Creación y Actualización del Registro

            // Relaciones
            $table->foreignId('fonograma_id')->references('id')->on('fonogramas');
            $table->foreignId('track_id')->references('id')->on('tracks');
        });
    }

    public function down()
    {
        Schema::dropIfExists('fonograma_track');
    }
}

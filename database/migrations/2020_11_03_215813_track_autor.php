<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TrackAutor extends Migration
{
    public function up()
    {
        Schema::create('track_autor', function (Blueprint $table) {
            $table->id()->index();  // Identificador Único de la Tabla: Parámetros

            $table->softDeletes();  // Eliminación lógica del Registro
            $table->timestamps();  // Fecha de Creación y Actualización del Registro

            // Relaciones
            $table->foreignId('track_id')->references('id')->on('tracks');
            $table->foreignId('autor_id')->references('id')->on('autores');
        });
    }

    public function down()
    {
        Schema::dropIfExists('track_autor');
    }
}

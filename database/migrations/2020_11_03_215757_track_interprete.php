<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TrackInterprete extends Migration
{
    public function up()
    {
        Schema::create('track_interprete', function (Blueprint $table) {
            $table->id()->index();  // Identificador Único de la Tabla: Parámetros
            $table->string('rolInterp')->nullable();  // Rol del Interprete

            $table->softDeletes();  // Eliminación lógica del Registro
            $table->timestamps();  // Fecha de Creación y Actualización del Registro

            // Relaciones
            $table->foreignId('track_id')->references('id')->on('tracks');
            $table->foreignId('interprete_id')->references('id')->on('interpretes');
        });
    }

    public function down()
    {
        Schema::dropIfExists('track_interprete');
    }
}

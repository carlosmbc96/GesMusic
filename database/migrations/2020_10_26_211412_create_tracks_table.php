<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTracksTable extends Migration
{
    public function up()
    {
        Schema::create('tracks', function (Blueprint $table) {
            // Migración del Modelo Tracks
            $table->id()->index();  // Identificador Único de la Tabla: Tracks
            $table->string('estd',8)->nullable();  // Estado Track *nom
            $table->string('ordenTrk')->nullable();  // Orden Track Repertorio
            $table->string('tituloTrk');  // Titulo Track
            $table->string('isrcTrk',15)->unique();  // ISRC Track
            $table->time('duracionTrk')->nullable();  // Duración Track
            $table->boolean('muestraTrk')->nullable();  // ¿El Track tiene una pista de Muestra?
            $table->boolean('envivoTrk')->nullable();  // ¿El Track se Grabó en Vivo?
            $table->string('generoTrk')->nullable();  // Género Musical Track *nom
            $table->string('subgeneroTrk')->nullable();  // Subgénero Musical Track *nom
            $table->boolean('bonusTrk')->nullable();  // ¿Es un bonus Track?
            $table->text('moodTrk')->nullable();  // Estados de Ánimos que refiere el Track *nom
            $table->string('gestionTrk')->nullable();  // Gestión Track *nom
            $table->string('paisgrabTrk')->nullable();  // País grabación Track *nom
            $table->string('archivoTrk')->nullable();  // Url del Archivo Track
            $table->string('archivoMuestraTrk')->nullable();  // Url del Archivo de MuestraTrack
            $table->string('dirArbolTrk')->nullable();  // Ruta URL del directorio donde se almacena el Track

            $table->softDeletes();  // Eliminación lógica del Registro
            $table->timestamps();  // Fecha de Creación y Actualización del Registro
        });
    }

    public function down()
    {
        Schema::dropIfExists('tracks');
    }
}

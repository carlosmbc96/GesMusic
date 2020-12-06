<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TemaAutor extends Migration
{
    public function up()
    {
        Schema::create('tema_autor', function (Blueprint $table) {
            $table->id()->index();  // Identificador Único de la Tabla: Parámetros

            $table->softDeletes();  // Eliminación lógica del Registro
            $table->timestamps();  // Fecha de Creación y Actualización del Registro

            // Relaciones
            $table->foreignId('tema_id')->references('id')->on('temas');
            $table->foreignId('autor_id')->references('id')->on('autores');
        });
    }

    public function down()
    {
        Schema::dropIfExists('tema_autor');
    }
}

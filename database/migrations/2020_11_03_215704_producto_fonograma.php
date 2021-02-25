<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ProductoFonograma extends Migration
{
    public function up()
    {
        Schema::create('producto_fonograma', function (Blueprint $table) {
            $table->id()->index();  // Identificador Único de la Tabla: Parámetros

            $table->softDeletes();  // Eliminación lógica del Registro
            $table->timestamps();  // Fecha de Creación y Actualización del Registro

            // Relaciones
            $table->foreignId('producto_id')->references('id')->on('productos')->onDelete("cascade");;
            $table->foreignId('fonograma_id')->references('id')->on('fonogramas');
        });
    }

    public function down()
    {
        Schema::dropIfExists('producto_fonograma');
    }
}

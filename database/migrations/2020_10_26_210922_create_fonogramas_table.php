<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFonogramasTable extends Migration
{
    public function up()
    {
        Schema::create('fonogramas', function (Blueprint $table) {
            // Migración del Modelo Fonogramas
            $table->id()->index();  // Identificador Único de la Tabla: Fonogramas
            $table->string('codigFong',8)->unique();  // Código Fonograma [FON0000]
            $table->string('tituloFong');  // Título Audiovisual
            $table->string('portadillaFong')->nullable();  // Url del Archivo de Portada del Audiovisual
            $table->string('clasficacionFong')->nullable();  // Clasificación Fonograma *nom
            $table->string('duracionFong')->nullable();  // Duración total del Fonograma (suma de las duraciones de los Tracks asociados)
            $table->string('territorioFong')->nullable();  // Territorio del Audiovisual
            $table->string('dueñoDerchFong')->nullable();  // Nombre y Apellidos del Dueño de los Derechos del Fonograma
            $table->string('nacioDueñoDerchFong')->nullable();  // Nacionalidad del Dueño de los Derechos del Fonograma
            $table->string('propiedadFong')->nullable();  // Nacionalidad Dueño Fonograma *nom
            $table->string('dirArbolFong')->nullable();  // Ruta URL del directorio donde se almacena el Fonograma
            $table->text('descripEspFong')->nullable();  // Descripción en Español del Fonograma
            $table->text('descripIngFong')->nullable();  // Descripción en Inglés del Fonograma

            $table->softDeletes();  // Eliminación lógica del Registro
            $table->timestamps();  // Fecha de Creación y Actualización del Registro
        });
    }

    public function down()
    {
        Schema::dropIfExists('fonogramas');
    }
}

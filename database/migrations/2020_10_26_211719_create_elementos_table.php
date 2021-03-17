<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateElementosTable extends Migration
{
    public function up()
    {
        Schema::create('elementos', function (Blueprint $table) {
            $table->id()->index();  // Identificador Único de la Tabla: Elementos
            $table->string('nombreElem');  // Nombre Elemento
            $table->string('tipoElem');  // Tipo Elemento *nom
            $table->string('dirArbolElem')->nullable();  // Ruta URL del directorio donde se almacena el Elemento
            $table->string('archivoElem');  // Url del Archivo del Elemento
            $table->text('descripElem')->nullable();  // Descripción Elemento

            $table->softDeletes();  // Eliminación lógica del Registro
            $table->timestamps();  // Fecha de Creación y Actualización del Registro

            // Relaciones
            $table->foreignId('producto_id')->nullable()->references('id')->on('productos');
            $table->foreignId('audiovisual_id')->nullable()->references('id')->on('audiovisuales');
            $table->foreignId('fonograma_id')->nullable()->references('id')->on('fonogramas');
        });
    }

    public function down()
    {
        Schema::dropIfExists('elementos');
    }
}

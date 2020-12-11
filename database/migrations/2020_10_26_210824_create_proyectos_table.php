<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProyectosTable extends Migration
{
    public function up()
    {
        Schema::create('proyectos', function (Blueprint $table) {
            // Migración del Modelo Proyectos
            $table->id()->index();  // Identificador Único de la Tabla: Proyectos
            $table->string('codigProy',9)->unique();  // Código Proyecto
            $table->string('nombreProy');  // Nombre Proyecto
            $table->string('identificadorProy')->nullable();  // Identificador del Proyecto
            $table->string('añoProy');  // Año Proyecto
            $table->string('dirArbolProy')->nullable();  // Ruta URL del directorio donde se almacena el Proyecto
            $table->text('descripEsp')->nullable();  // Descripción Español Proyecto
            $table->text('descripIng')->nullable();  // Descripción Inglés Proyecto

            $table->softDeletes();  // Eliminación lógica del Registro
            $table->timestamps();  // Fecha de Creación y Actualización del Registro

            // Relaciones
            $table->foreignId('empresa_id')->references('id')->on('empresas');
        });
    }

    public function down()
    {
        Schema::dropIfExists('proyectos');
    }
}

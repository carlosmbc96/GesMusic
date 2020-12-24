<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAutoresTable extends Migration
{
    public function up()
    {
        Schema::create('autores', function (Blueprint $table) {
            $table->id()->index();  // Identificador Único de la Tabla: Autores
            $table->string('ciAutr',11)->unique();  // Número Identidad Autor
            $table->string('codigAutr',9)->unique();  // Código Autor
            $table->string('fotoAutr')->nullable();  // Url del Archivo de Foto Autor
            $table->string('nombresAutr');  // Nombres Autor
            $table->string('apellidosAutr');  // Apellidos Autor
            $table->string('sexoAutr');  // Sexo Autor *nom
            $table->boolean('fallecidoAutr')->nullable();  // ¿El Autor es Fallecido?
            $table->boolean('obrasCatEditAutr')->nullable();  // ¿El Autor tiene Obras en el Catalgo Editorial de Bismusic?
            $table->text('reseñaBiogAutr')->nullable();  // Reseña Biográfica Autor

            $table->softDeletes();  // Eliminación lógica del Registro
            $table->timestamps();  // Fecha de Creación y Actualización del Registro
        });
    }

    public function down()
    {
        Schema::dropIfExists('autores');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInterpretesTable extends Migration
{
    public function up()
    {
        Schema::create('interpretes', function (Blueprint $table) {
            $table->id()->index();  // Identificador Único de la Tabla: Intérpretes
            $table->string('codigInterp',9)->unique();  // Código Autor
            $table->string('nombreInterp');  // Nombre Intérprete
            $table->text('reseñaBiogInterp')->nullable();  // Reseña Biográfica Intérprete

            $table->softDeletes();  // Eliminación lógica del Registro
            $table->timestamps();  // Fecha de Creación y Actualización del Registro
        });
    }

    public function down()
    {
        Schema::dropIfExists('interpretes');
    }
}

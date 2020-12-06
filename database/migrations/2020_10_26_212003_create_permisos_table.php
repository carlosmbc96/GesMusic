<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePermisosTable extends Migration
{
    public function up()
    {
        Schema::create('permisos', function (Blueprint $table) {
            $table->id()->index();  // Identificador Único de la Tabla: Permisos
            $table->string('nombrePerm');  // Nombre Permiso
            $table->string('slugPerm')->nullable();  // Slug Permiso
            $table->text('descripPerm')->nullable();  // Descripción Permiso

            $table->softDeletes();  // Eliminación lógica del Registro
            $table->timestamps();  // Fecha de Creación y Actualización del Registro
        });
    }

    public function down()
    {
        Schema::dropIfExists('permisos');
    }
}

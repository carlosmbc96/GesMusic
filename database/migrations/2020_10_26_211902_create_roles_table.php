<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRolesTable extends Migration
{
    public function up()
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id()->index();  // Identificador Único de la Tabla: Roles
            $table->string('nombreRol');  // Nombre Rol
            $table->text('descripRol')->nullable();  // Descripción Rol
            $table->boolean('especial')->nullable();  // ¿El Rol es Espacial?

            $table->softDeletes();  // Eliminación lógica del Registro
            $table->timestamps();  // Fecha de Creación y Actualización del Registro
        });
    }

    public function down()
    {
        Schema::dropIfExists('roles');
    }
}

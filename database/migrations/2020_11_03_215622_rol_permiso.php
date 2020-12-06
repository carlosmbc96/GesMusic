<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RolPermiso extends Migration
{
    public function up()
    {
        Schema::create('rol_permiso', function (Blueprint $table) {
            $table->id()->index();  // Identificador Único de la Tabla: Parámetros

            $table->softDeletes();  // Eliminación lógica del Registro
            $table->timestamps();  // Fecha de Creación y Actualización del Registro

            // Relaciones
            $table->foreignId('rol_id')->references('id')->on('roles');
            $table->foreignId('permiso_id')->references('id')->on('permisos');
        });
    }

    public function down()
    {
        Schema::dropIfExists('rol_permiso');
    }
}

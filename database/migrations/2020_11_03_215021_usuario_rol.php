<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UsuarioRol extends Migration
{
    public function up()
    {
        Schema::create('usuario_rol', function (Blueprint $table) {
            $table->id()->index();  // Identificador Único de la Tabla: Parámetros

            $table->softDeletes();  // Eliminación lógica del Registro
            $table->timestamps();  // Fecha de Creación y Actualización del Registro

            // Relaciones
            $table->foreignId('usuario_id')->references('id')->on('usuarios');
            $table->foreignId('rol_id')->references('id')->on('roles');
        });
    }

    public function down()
    {
        Schema::dropIfExists('usuario_rol');
    }
}

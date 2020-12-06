<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsuariosTable extends Migration
{
    public function up()
    {
        Schema::create('usuarios', function (Blueprint $table) {
            $table->id()->index();  // Identificador Único de la Tabla: Usuarios
            $table->string('estadoUser')->nullable();  // Estado Gestión Usuario *nom
            $table->string('nombreUser');  // Nombre Usuario
            $table->string('emailUser');  // Correo del Usuario
            $table->timestamp('verificaEmailUser')->nullable(); // Verifica Correo del Usuario
            $table->string('contraseñaUser');  // Contraseña Usuario Empleado
            $table->date('fechaCaduc');  // Fecha Caducidad Contraseña Usuario Empleado

            $table->softDeletes();  // Eliminación lógica del Registro
            $table->timestamps();  // Fecha de Creación y Actualización del Registro

            // Relaciones
            $table->foreignId('empleado_id')->references('id')->on('empleados');
        });
    }

    public function down()
    {
        Schema::dropIfExists('usuarios');
    }
}

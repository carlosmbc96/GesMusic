<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmpleadosTable extends Migration
{
    public function up()
    {
        Schema::create('empleados', function (Blueprint $table) {
            $table->id()->index();  // Identificador Único de la Tabla: Empleados
            $table->integer('ciEmpl')->unique();  // Número Identidad Empleado
            $table->string('fotoEmpl')->nullable();  // Url del Archivo de Foto Empleado
            $table->string('nombresEmpl');  // Nombres Empleado
            $table->string('apellidosEmpl');  // Apellidos Empleado
            $table->string('sexoEmpl');  // Sexo Empleado *nom
            $table->date('fechaNacEmpl')->nullable();  // Fecha Nacimiento Empleado
            $table->string('empresaEmpl');  // Empresa Empleado
            $table->string('cargoEmpl')->nullable();  // Cargo Empleado
            $table->string('DptoEmpl')->nullable();  // Departamento Empleado
            $table->string('direccionEmpl')->nullable();  // Direción de Residencia Empleado
            $table->string('celEmpl')->nullable();  // Móvil Empleado
            $table->string('telfEmpl')->nullable();  // Teléfono Fijo Empleado
            $table->string('emailEmpl')->nullable();  // Correo Electrónico Empleado
            $table->string('webSocialEmpl')->nullable();  // Url Web Social Empleado
            $table->text('descripEmpl')->nullable();  // Descripción Empleado

            $table->softDeletes();  // Eliminación lógica del Registro
            $table->timestamps();  // Fecha de Creación y Actualización del Registro

            // Relaciones
            $table->foreignId('empresa_id')->references('id')->on('empresas');
        });
    }

    public function down()
    {
        Schema::dropIfExists('empleados');
    }
}

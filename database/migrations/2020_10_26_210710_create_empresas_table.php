<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmpresasTable extends Migration
{
    public function up()
    {
        Schema::create('empresas', function (Blueprint $table) {
            // Migración del Modelo Empresa
            $table->id()->index();  // Identificador Único de la Tabla: Empresa
            $table->string('codEmpr',9)->unique();  // Código Empresa
            $table->string('logoEmpr')->nullable();  // Url del Archivo de Logo Empresa
            $table->string('nombreEmpr');  // Nombre Empresa
            $table->string('siglasEmpr')->nullable();  // Siglas Empresa
            $table->string('tipoEmpr')->nullable();  // Tipo Empresa *nom
            $table->string('persJurEmpr')->nullable();  // Pers. Juridica Empresa *nom
            $table->date('fechaCreaEmpr')->nullable();  // Fecha Creación Empresa
            $table->text('direccionEmpr')->nullable();  // Dirección Legal Empresa
            $table->string('telFijo1Empr')->nullable();  // Teléfono Fijo # 1 Empresa
            $table->string('telFijo2Empr')->nullable();  // Teléfono Fijo # 2 Empresa
            $table->string('email1Empr')->nullable();  // Correo # 1 Empresa
            $table->string('email2Empr')->nullable();  // Correo # 2 Empresa
            $table->string('webOficialEmpr')->nullable();  // Web Oficial Empresa
            $table->string('socialWeb1Empr')->nullable();  // Web Social # 1 Empresa
            $table->string('socialWeb2Empr')->nullable();  // Web Social # 2 Empresa
            $table->string('socialWeb3Empr')->nullable();  // Web Social # 3 Empresa
            $table->string('socialWeb4Empr')->nullable();  // Web Social # 4 Empresa
            $table->text('descripEmpr')->nullable();  // Descripción Empresa
            $table->text('otrosDatosEmpr')->nullable();  // Otros Datos Empresa

            $table->softDeletes();  // Eliminación lógica del Registro
            $table->timestamps();  // Fecha de Creación y Actualización del Registro
        });
    }

    public function down()
    {
        Schema::dropIfExists('empresas');
    }
}

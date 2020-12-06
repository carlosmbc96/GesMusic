<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductosTable extends Migration
{
    public function up()
    {
        Schema::create('productos', function (Blueprint $table) {
            // Migración del Modelo Productos
            $table->id()->index();  // Identificador Único de la Tabla: Productos
            $table->string('codigProd',8)->unique();  // Código Producto [B0000]
            $table->string('tituloProd');  // Título Producto
            $table->string('identificadorProd')->nullable();  // Identificador del Producto
            $table->string('añoProd');  // Año Producto *nom
            $table->string('portadillaProd')->nullable();  // Url del Archivo de Foto Portada Producto
            $table->string('sellodiscProd')->nullable();  // Sello Discográfico del Producto *nom
            $table->string('estadodigProd')->nullable();  // Estado de Digitalización Producto *nom
            $table->string('statusComProd')->nullable();  // Estatud Comercial Producto *nom
            $table->string('destinosComerPro')->nullable();  // Destino Comercial *nom
            $table->boolean('producPrincProd')->nullable();  // ¿Producto Principal del Proyecto?
            $table->string('codigBarProd',13)->nullable();  // Código de Barra Producto
            $table->string('genMusicPro')->nullable();  // Género Musical Producto *nom
            $table->boolean('activoCatalbisPro')->nullable();  // ¿Activo en el Catalogo de Bismusic el Producto?
            $table->boolean('catalDigitalPro')->nullable();  // ¿Estará el Producto en el Catalogo Digital?
            $table->boolean('primeraPantProd')->nullable();  // ¿Estará el Producto en la Primera Pantalla?
            $table->boolean('variosInterpretesProd')->nullable();  // ¿El Producto tiene varios Intérpretes?
            $table->string('interpretesProd')->nullable();  // Intérpretes del Producto
            $table->string('autoresProd')->nullable();  // Autores del Producto
            $table->string('dirArbolProd')->nullable();  // Ruta URL del directorio donde se almacena el Producto
            $table->text('descripEspPro')->nullable();  // Descripción Español Producto
            $table->text('descripIngPro')->nullable();  // Descripción Inglés Producto

            $table->softDeletes();  // Eliminación lógica del Registro
            $table->timestamps();  // Fecha de Creación y Actualización del Registro

            // Relaciones
            $table->foreignId('proyecto_id')->references('id')->on('proyectos')->onDelete("cascade");
        });
    }

    public function down()
    {
        Schema::dropIfExists('productos');
    }
}

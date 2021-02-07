<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAudiovisualesTable extends Migration
{
    public function up()
    {
        Schema::create('audiovisuales', function (Blueprint $table) {
            // Migración del Modelo Audiovisuales
            $table->id()->index();  // Identificador Único de la Tabla: Audiovisuales
            $table->string('codigAud',9)->unique();  // Código Audiovisual [AUD0000]
            $table->string('isrcAud',12)->unique()->nullable();  // ISRC Audiovisual
            $table->string('tituloAud');  // Título Audiovisual
            $table->string('portadillaAud')->nullable();  // Url del Archivo de Portada del Audiovisual
            $table->string('clasifAud')->nullable();  // Clasificación Audiovisual *nom
            $table->string('generoAud')->nullable();  // Género Audiovisual *nom
            $table->time('duracionAud')->nullable();  // Duración Audiovisual
            $table->string('añoFinAud')->nullable();  // Año que se terminó el Audiovisual *nom
            $table->string('paisGrabAud')->nullable();  // País de Grabación del Audiovisual *nom
            $table->string('idiomaAud')->nullable();  // Idioma Audiovisual *nom
            $table->string('subtitulosAud')->nullable();  // Subtitulos Audiovisual *nom
            $table->string('fenomRefAud')->nullable();  // Fenómeno de Referencia del Audiovisual
            $table->text('etiquetasAud')->nullable();  // Etiquetas del Audiovisual *nom
            $table->string('archivoAud')->nullable();  // Url del Archivo Audiovisual Media
            $table->string('dueñoDerchAud')->nullable();  // Nombre y Apellidos del Dueño de los Derechos del Audivisual
            $table->string('nacioDueñoDerchAud')->nullable();  // Nacionalidad del Dueño de los Derechos del Audivisual
            $table->string('derechosAud')->nullable();  // Nacionalidad Dueño Producto *nom
            $table->boolean('makingOfAud')->nullable();  // ¿El Audiovisual tiene Making-Of?
            $table->string('dirArbolAud')->nullable();  // Ruta URL del directorio donde se almacena el Audiovisual
            $table->text('descripEspAud')->nullable();  // Descripción en Español Media
            $table->text('descripIngAud')->nullable();  // Descripción en Inglés Media

            $table->softDeletes();  // Eliminación lógica del Registro
            $table->timestamps();  // Fecha de Creación y Actualización del Registro
        });
    }

    public function down()
    {
        Schema::dropIfExists('audiovisuales');
    }
}

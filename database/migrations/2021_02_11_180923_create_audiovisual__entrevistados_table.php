<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAudiovisualEntrevistadosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('audiovisual_entrevistado', function (Blueprint $table) {
            $table->id();
            $table->softDeletes();  // Eliminación lógica del Registro
            $table->timestamps();
            // Relaciones
            $table->foreignId('entrevistado_id')->references('id')->on('entrevistados');
            $table->foreignId('audiovisual_id')->references('id')->on('audiovisuales');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('audiovisual__entrevistados');
    }
}

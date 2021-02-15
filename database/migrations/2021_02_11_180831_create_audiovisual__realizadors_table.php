<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAudiovisualRealizadorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('audiovisual_realizador', function (Blueprint $table) {
            $table->id();
            $table->softDeletes();  // Eliminación lógica del Registro
            $table->timestamps();
            // Relaciones
            $table->foreignId('realizador_id')->references('id')->on('realizadores');
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
        Schema::dropIfExists('audiovisual__realizadors');
    }
}

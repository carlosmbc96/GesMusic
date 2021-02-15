<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Audiovisual_Realizador extends Model
{
    //SECCIÓN DE PROTECT TABLE
    protected $table = "audiovisual_realizador";
    //SECCIÓN DE PROTECT TABLE

    //SECCIÓN DE FILLABLE
    // Atributos del Modelo Producto
    protected $fillable = [
        'audiovisual_id',
        'realizador_id',
    ];
    //SECCIÓN DE FILLABLE
}

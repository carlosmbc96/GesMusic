<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Audiovisual_Entrevistado extends Model
{
    //SECCIÓN DE PROTECT TABLE
    protected $table = "audiovisual_entrevistado";
    //SECCIÓN DE PROTECT TABLE

    //SECCIÓN DE FILLABLE
    // Atributos del Modelo Producto
    protected $fillable = [
        'audiovisual_id',
        'entrevistado_id',
    ];
    //SECCIÓN DE FILLABLE
}

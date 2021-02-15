<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Audiovisual_Interprete extends Model
{
    //SECCIÓN DE PROTECT TABLE
    protected $table = "audiovisual_interprete";
    //SECCIÓN DE PROTECT TABLE

    //SECCIÓN DE FILLABLE
    // Atributos del Modelo Producto
    protected $fillable = [
        'audiovisual_id',
        'interprete_id',
    ];
    //SECCIÓN DE FILLABLE
}

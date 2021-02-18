<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Track_Interprete extends Model
{
    //SECCIÓN DE PROTECT TABLE
    protected $table = "track_interprete";
    //SECCIÓN DE PROTECT TABLE

    //SECCIÓN DE FILLABLE
    // Atributos del Modelo Producto
    protected $fillable = [
        'track_id',
        'interprete_id',
        'rollInterp'
    ];
    //SECCIÓN DE FILLABLE
}

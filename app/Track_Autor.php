<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Track_Autor extends Model
{
    //SECCIÓN DE PROTECT TABLE
    protected $table = "track_autor";
    //SECCIÓN DE PROTECT TABLE

    //SECCIÓN DE FILLABLE
    // Atributos del Modelo Producto
    protected $fillable = [
        'track_id',
        'autor_id',
    ];
    //SECCIÓN DE FILLABLE
}

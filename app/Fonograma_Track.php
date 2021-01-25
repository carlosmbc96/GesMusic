<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Fonograma_Track extends Model
{
    //SECCIÓN DE PROTECT TABLE
    protected $table = "fonograma_track";
    //SECCIÓN DE PROTECT TABLE

    //SECCIÓN DE FILLABLE
    // Atributos del Modelo Producto
    protected $fillable = [
        'fonograma_id',
        'track_id'
    ];
    //SECCIÓN DE FILLABLE
}

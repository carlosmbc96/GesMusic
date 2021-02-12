<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Audiovisual_Autor extends Model
{
    //SECCIÓN DE PROTECT TABLE
    protected $table = "audiovisual_autor";
    //SECCIÓN DE PROTECT TABLE

    //SECCIÓN DE FILLABLE
    // Atributos del Modelo Producto
    protected $fillable = [
        'audiovisual_id',
        'autor_id',
    ];
    //SECCIÓN DE FILLABLE
}

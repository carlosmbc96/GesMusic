<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Producto_Audiovisual extends Model
{

    //SECCIÓN DE PROTECT TABLE
    protected $table = "producto_audiovisual";
    //SECCIÓN DE PROTECT TABLE

    //SECCIÓN DE FILLABLE
    // Atributos del Modelo Producto
    protected $fillable = [
        'producto_id',
        'audiovisual_id'
    ];
    //SECCIÓN DE FILLABLE
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Producto_Fonograma extends Model
{

    //SECCIÓN DE PROTECT TABLE
    protected $table = "producto_fonograma";
    //SECCIÓN DE PROTECT TABLE

    //SECCIÓN DE FILLABLE
    // Atributos del Modelo Producto
    protected $fillable = [
        'producto_id',
        'fonograma_id'
    ];
    //SECCIÓN DE FILLABLE
}

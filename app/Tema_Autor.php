<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tema_Autor extends Model
{
    //SECCIÓN DE PROTECT TABLE
    protected $table = "tema_autor";
    //SECCIÓN DE PROTECT TABLE

    //SECCIÓN DE FILLABLE
    // Atributos del Modelo Producto
    protected $fillable = [
        'tema_id',
        'autor_id'
    ];
    //SECCIÓN DE FILLABLE
}

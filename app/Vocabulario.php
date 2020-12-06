<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vocabulario extends Model
{
    // Método de Eliminación Lógica del Modelo Empresa
    use SoftDeletes;


    //SECCIÓN DE FILLABLE
    // Atributos del Modelo Vocabulario
    protected $fillable = [
        'acronimoVoc',  // Acrónimo Vocabulario
        'nombreVoc',  // Nombre Vocabulario
        'descripVoc',  // Descripción Vocabulario
    ];
    //SECCIÓN DE FILLABLE


    //SECCIÓN DE RELACIONES
    //Relación de One to Many Vocabularios - Terminos
    public function terminos()
    {
        return $this->hasMany(Termino::class); // Un Vocabulario tiene muchos Terminos
    }
    //SECCIÓN DE RELACIONES


    //SECCIÓN DE QUERY SCOPE
    // Query Scope que devuelve el Total de Registros del Modelo Vocabulario
    public function scopeTotalRegistros($query)
    {
        return $query->count();
    }

    // Query Scope que devuelve el Primer registro del Modelo Vocabulario
    public function scopePrimerRegistro($query)
    {
        return $query->oldest()->first();
    }

    // Query Scope que devuelve el Último registro del Modelo Vocabulario
    public function scopeUltimoRegistro($query)
    {
        return $query->latest()->first();
    }

    // Query Scope que devuelve el Total de Registros Eliminados de forma Lógica del Modelo Vocabulario
    public function scopeTotalEliminadosLogico($query)
    {
        return $query->onlyTrashed()->count();
    }

    // Query Scope que devuelve todos los Registros Eliminados de forma Lógica del Modelo Vocabulario
    public function scopeEliminadosLogico($query)
    {
        return $query->onlyTrashed();
    }

    // Query Scope que devuelve los registros de una consulta Específica del Modelo Vocabulario
    public function scopeBusqSelect($query, $atributo, $valorbuscado)
    {
        if (($atributo) && ($valorbuscado))
        {
            return $query->where($atributo,'like',"%$valorbuscado%");
        }
    }

    // Query Scope que devuelve los registros de una consulta General del Modelo Vocabulario
    public function scopeBusqGeneral($query, $valorbuscado)
    {
        if ($valorbuscado)
        {
            return $query
                ->orwhere('nombreVoc','like',"%$valorbuscado%")
                ->orwhere('acronimoVoc','like',"%$valorbuscado%");
        }
    }
    //SECCIÓN DE QUERY SCOPE

}

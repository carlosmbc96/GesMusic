<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Termino extends Model
{
    // Método de Eliminación Lógica del Modelo Empresa
    use SoftDeletes;


    //SECCIÓN DE FILLABLE
    // Atributos del Modelo Termino
    protected $fillable = [
        'acronimoTer',  // Acrónimo Término
        'nombreTer',  // Nombre Término
        'descripTer',  // Descripción Término

        'vocabulario_id',  // Identificador Único de la tabla Vocabulario  / Relación One to Many
    ];
    //SECCIÓN DE FILLABLE


    //SECCIÓN DE RELACIONES
    //Relación de Many to One Terminos - Vocabularios
    public function vocabularios()
    {
        return $this->belongsTo(Vocabulario::class); // Un Termino pertenece a un Vocabulario
    }
    //SECCIÓN DE RELACIONES


    //SECCIÓN DE QUERY SCOPE
    // Query Scope que devuelve el Total de Registros del Modelo Termino
    public function scopeTotalRegistros($query)
    {
        return $query->count();
    }

    // Query Scope que devuelve el Primer registro del Modelo Termino
    public function scopePrimerRegistro($query)
    {
        return $query->oldest()->first();
    }

    // Query Scope que devuelve el Último registro del Modelo Termino
    public function scopeUltimoRegistro($query)
    {
        return $query->latest()->first();
    }

    // Query Scope que devuelve el Total de Registros Eliminados de forma Lógica del Modelo Termino
    public function scopeTotalEliminadosLogico($query)
    {
        return $query->onlyTrashed()->count();
    }

    // Query Scope que devuelve todos los Registros Eliminados de forma Lógica del Modelo Termino
    public function scopeEliminadosLogico($query)
    {
        return $query->onlyTrashed();
    }

    // Query Scope que devuelve los registros de una consulta Específica del Modelo Termino
    public function scopeBusqSelect($query, $atributo, $valorbuscado)
    {
        if (($atributo) && ($valorbuscado))
        {
            return $query->where($atributo,'like',"%$valorbuscado%");
        }
    }

    // Query Scope que devuelve los registros de una consulta General del Modelo Termino
    public function scopeBusqGeneral($query, $valorbuscado)
    {
        if ($valorbuscado)
        {
            return $query
                ->orwhere('nombreTer','like',"%$valorbuscado%")
                ->orwhere('acronimoTer','like',"%$valorbuscado%");
        }
    }
    //SECCIÓN DE QUERY SCOPE

}

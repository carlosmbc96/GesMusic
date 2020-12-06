<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Artistico extends Model
{
    // Método de Eliminación Lógica del Modelo Empresa
    use SoftDeletes;


    //SECCIÓN DE FILLABLE
    // Atributos del Modelo Artistico
    protected $fillable = [
        'NombreArts',  // Nombre Artistico
        'actualNombreArts',  // ¿Es el Actual Nombre Artístico?
        'descripNombreArts',  // Descripción Nombre Artístico

        'interprete_id',  // Identificador Único de la tabla Interpretes  / Relación One to Many
    ];
    //SECCIÓN DE FILLABLE


    //SECCIÓN DE RELACIONES
    //Relación de Many to One Nombres Artisticos - Interpretes
    public function interpretes()
    {
        return $this->belongsTo(Interprete::class); // Un Nombre Artistico esta asociado a un Interprete
    }
    //SECCIÓN DE RELACIONES



    //SECCIÓN DE QUERY SCOPE
    // Query Scope que devuelve el Primer registro del Modelo Artistico
    public function scopePrimerRegistro($query)
    {
        return $query->oldest()->first();
    }

    // Query Scope que devuelve el Último registro del Modelo Artistico
    public function scopeUltimoRegistro($query)
    {
        return $query->latest()->first();
    }

    // Query Scope que devuelve el Total de Registros Eliminados de forma Lógica del Modelo Artistico
    public function scopeTotalEliminadosLogico($query)
    {
        return $query->onlyTrashed()->count();
    }

    // Query Scope que devuelve todos los Registros Eliminados de forma Lógica del Modelo Artistico
    public function scopeEliminadosLogico($query)
    {
        return $query->onlyTrashed();
    }

    // Query Scope que devuelve los registros de una consulta Específica del Modelo Artistico
    public function scopeBusqSelect($query, $atributo, $valorbuscado)
    {
        if (($atributo) && ($valorbuscado))
        {
            return $query->where($atributo,'like',"%$valorbuscado%");
        }
    }

    // Query Scope que devuelve los registros de una consulta General del Modelo Artistico
    public function scopeBusqGeneral($query, $valorbuscado)
    {
        if ($valorbuscado)
        {
            return $query
                ->orwhere('NombreArts','like',"%$valorbuscado%")
                ->orwhere('actualNombreArts','like',"%$valorbuscado%");
        }
    }
    //SECCIÓN DE QUERY SCOPE
}

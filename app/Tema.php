<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tema extends Model
{
    // Método de Eliminación Lógica del Modelo Empresa
    use SoftDeletes;


    //SECCIÓN DE FILLABLE
    // Atributos del Modelo Tema
    protected $fillable = [
				'codigTema',
        'tituloTem',  // Título Tema
        'catalDigitalTem',  // ¿Estará Tema en el Catálogo Digital?
        'sociedadGestionTem',  // Sociedad de Gestión Tema *nom
        'descripTem',  // Descripción Tema

        'track_id',  // Identificador Único de la tabla Tracks  / Relación One to Many
    ];
    //SECCIÓN DE FILLABLE

    //SECCIÓN DE PROTECT TABLE
    protected $table = "temas" ;
    //SECCIÓN DE PROTECT TABLE

    //SECCIÓN DE RELACIONES
    //Relación de Many to One Temas - Tracks
    public function track()
    {
        return $this->belongsTo(Track::class); // Un Tema tiene asociado un Track
    }
    //SECCIÓN DE RELACIONES


    //SECCIÓN DE QUERY SCOPE
    // Query Scope que devuelve el Total de Registros del Modelo Tema
    public function scopeTotalRegistros($query)
    {
        return $query->count();
    }

    // Query Scope que devuelve el Primer registro del Modelo Tema
    public function scopePrimerRegistro($query)
    {
        return $query->oldest()->first();
    }

    // Query Scope que devuelve el Último registro del Modelo Tema
    public function scopeUltimoRegistro($query)
    {
        return $query->latest()->first();
    }

    // Query Scope que devuelve el Total de Registros Eliminados de forma Lógica del Modelo Tema
    public function scopeTotalEliminadosLogico($query)
    {
        return $query->onlyTrashed()->count();
    }

    // Query Scope que devuelve todos los Registros Eliminados de forma Lógica del Modelo Tema
    public function scopeEliminadosLogico($query)
    {
        return $query->onlyTrashed();
    }

    // Query Scope que devuelve los registros de una consulta Específica del Modelo Tema
    public function scopeBusqSelect($query, $atributo, $valorbuscado)
    {
        if (($atributo) && ($valorbuscado))
        {
            return $query->where($atributo,'like',"%$valorbuscado%");
        }
    }

    // Query Scope que devuelve los registros de una consulta General del Modelo Tema
    public function scopeBusqGeneral($query, $valorbuscado)
    {
        if ($valorbuscado)
        {
            return $query
                ->orwhere('tituloTem','like',"%$valorbuscado%")
                ->orwhere('catalDigitalTem','like',"%$valorbuscado%")
                ->orwhere('sociedadGestionTem','like',"%$valorbuscado%");
        }
    }
    //SECCIÓN DE QUERY SCOPE

}

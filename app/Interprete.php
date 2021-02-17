<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Interprete extends Model
{
    // Método de Eliminación Lógica del Modelo Empresa
    use SoftDeletes;


    //SECCIÓN DE FILLABLE
    // Atributos del Modelo Interprete
    protected $fillable = [
        'nombreInterp',  // Nombre Intérprete
        'codigInterp',  // Código Intérprete
        'reseñaBiogInterp',  // Reseña Biográfica Intérprete
    ];
    //SECCIÓN DE FILLABLE

    //SECCIÓN DE PROTECT TABLE
    protected $table = "interpretes" ;
    //SECCIÓN DE PROTECT TABLE

    //SECCIÓN DE RELACIONES
    //Relación de One to Many Interpretes - Nombres Artisticos
    public function artisticos()
    {
        return $this->hasMany(Artistico::class); // Un Interprete tiene muchos Nombres Artisticos
    }
    //Relación de One to Many Audiovisuales - Realizadores
    public function audiovisuales()
    {
        return $this->belongsToMany(Audiovisual::class, 'audiovisual_interprete'); // Un Audiovisual tiene muchos Realizadores
    }
    public function tracks()
    {
        return $this->belongsToMany(Track::class, 'track_interprete'); // Un Audiovisual tiene muchos Realizadores
    }
    //SECCIÓN DE RELACIONES


    //SECCIÓN DE QUERY SCOPE
    // Query Scope que devuelve el Total de Registros del Modelo Interprete
    public function scopeTotalRegistros($query)
    {
        return $query->count();
    }

    // Query Scope que devuelve el Primer registro del Modelo Interprete
    public function scopePrimerRegistro($query)
    {
        return $query->oldest()->first();
    }

    // Query Scope que devuelve el Último registro del Modelo Interprete
    public function scopeUltimoRegistro($query)
    {
        return $query->latest()->first();
    }

    // Query Scope que devuelve el Total de Registros Eliminados de forma Lógica del Modelo Interprete
    public function scopeTotalEliminadosLogico($query)
    {
        return $query->onlyTrashed()->count();
    }

    // Query Scope que devuelve todos los Registros Eliminados de forma Lógica del Modelo Interprete
    public function scopeEliminadosLogico($query)
    {
        return $query->onlyTrashed();
    }

    // Query Scope que devuelve los registros de una consulta Específica del Modelo Interprete
    public function scopeBusqSelect($query, $atributo, $valorbuscado)
    {
        if (($atributo) && ($valorbuscado))
        {
            return $query->where($atributo,'like',"%$valorbuscado%");
        }
    }

    // Query Scope que devuelve los registros de una consulta General del Modelo Interprete
    public function scopeBusqGeneral($query, $valorbuscado)
    {
        if ($valorbuscado)
        {
            return $query
                ->orwhere('nombreInterp','like',"%$valorbuscado%")
                ->orwhere('codigInterp','like',"%$valorbuscado%");
        }
    }

}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Fonograma extends Model
{
    // Método de Eliminación Lógica del Modelo Empresa
    use SoftDeletes;


    //SECCIÓN DE FILLABLE
    // Atributos del Modelo Fonograma
    protected $fillable = [
        'codigFong',  // Código Fonograma [FON0000]
        'tituloFong',  // Título Audiovisual
        'portadillaFong',  // Url del Archivo de Portada del Audiovisual
        'clasficacionFong',  // Clasificación Fonograma *nom
        'duracionFong',  // Duración total del Fonograma (suma de las duraciones de los Tracks asociados)
        'territorioFong',  // Territorio del Fonograma *nom
        'dueñoDerchFong',  // Nombre y Apellidos del Dueño de los Derechos del Fonograma
        'nacioDueñoDerchFong',  // Nacionalidad del Dueño de los Derechos del Fonograma *nom
        'propiedadFong',  // Nacionalidad Dueño Fonograma *nom
        'dirArbolFong',  // Ruta URL del directorio donde se almacena el Fonograma
        'descripEspFong',  // Descripción en Español del Fonograma
        'descripIngFong',  // Descripción en Inglés del Fonograma

        'producto_id',  // Identificador Único de la tabla Producto  / Relación One to Many
    ];
    //SECCIÓN DE FILLABLE


    //SECCIÓN DE RELACIONES
    //Relación de One to Many Fonogramas - Elementos
    public function elementos()
    {
        return $this->hasMany(Elemento::class); // Un Fonograma tiene muchos Elementos
    }
    //SECCIÓN DE RELACIONES


    //SECCIÓN DE QUERY SCOPE
    // Query Scope que devuelve el Total de Registros del Modelo Fonograma
    public function scopeTotalRegistros($query)
    {
        return $query->count();
    }

    // Query Scope que devuelve el Primer registro del Modelo Fonograma
    public function scopePrimerRegistro($query)
    {
        return $query->oldest()->first();
    }

    // Query Scope que devuelve el Último registro del Modelo Fonograma
    public function scopeUltimoRegistro($query)
    {
        return $query->latest()->first();
    }

    // Query Scope que devuelve el Total de Registros Eliminados de forma Lógica del Modelo Fonograma
    public function scopeTotalEliminadosLogico($query)
    {
        return $query->onlyTrashed()->count();
    }

    // Query Scope que devuelve todos los Registros Eliminados de forma Lógica del Modelo Fonograma
    public function scopeEliminadosLogico($query)
    {
        return $query->onlyTrashed();
    }

    // Query Scope que devuelve los registros de una consulta Específica del Modelo Fonograma
    public function scopeBusqSelect($query, $atributo, $valorbuscado)
    {
        if (($atributo) && ($valorbuscado))
        {
            return $query->where($atributo,'like',"%$valorbuscado%");
        }
    }

    // Query Scope que devuelve los registros de una consulta General del Modelo Fonograma
    public function scopeBusqGeneral($query, $valorbuscado)
    {
        if ($valorbuscado)
        {
            return $query
                ->orwhere('codigFong','like',"%$valorbuscado%")
                ->orwhere('tituloFong','like',"%$valorbuscado%")
                ->orwhere('clasficacionFong','like',"%$valorbuscado%")
                ->orwhere('territorioFong','like',"%$valorbuscado%")
                ->orwhere('dueñoDerchFong','like',"%$valorbuscado%")
                ->orwhere('nacioDueñoDerchFong','like',"%$valorbuscado%")
                ->orwhere('propiedadFong','like',"%$valorbuscado%");
        }
    }
    //SECCIÓN DE QUERY SCOPE

}

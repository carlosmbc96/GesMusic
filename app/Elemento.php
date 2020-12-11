<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Elemento extends Model
{
    // Método de Eliminación Lógica del Modelo Empresa
    use SoftDeletes;


    //SECCIÓN DE FILLABLE
    // Atributos del Modelo Elemento
    protected $fillable = [
        'nombreElem',  // Nombre Elemento
        'tipoElem',  // Tipo Elemento *nom
        'dirArbolElem',  // Ruta URL del directorio donde se almacena el Elemento
        'archivoElem',  // Url del Archivo del Elemento
        'descripElem',  // Descripción Elemento

        'producto_id',  // Identificador Único de la tabla Productos  / Relación One to Many
        'audiovisual_id',  // Identificador Único de la tabla Audiovisuales  / Relación One to Many
        'fonograma_id',  // Identificador Único de la tabla Fonogramas  / Relación One to Many
    ];
    //SECCIÓN DE FILLABLE

    //SECCIÓN DE PROTECT TABLE
    protected $table = "elementos" ;
    //SECCIÓN DE PROTECT TABLE

    //SECCIÓN DE RELACIONES
    //Relación de Many to One Elementos - Productos
    public function producto()
    {
        return $this->belongsTo(Producto::class); // Un Elemento pertenece a un Producto
    }
    //Relación de Many to One Elementos - Productos
    public function audiovisual()
    {
        return $this->belongsTo(Audiovisual::class); // Un Elemento pertenece a un Audiovisual
    }
    //Relación de Many to One Elementos - Productos
     public function fonograma()
    {
        return $this->belongsTo(Fonograma::class); // Un Elemento pertenece a un Fonograma
    }
    //SECCIÓN DE RELACIONES


    //SECCIÓN DE QUERY SCOPE
    // Query Scope que devuelve el Total de Registros del Modelo Elemento
    public function scopeTotalRegistros($query)
    {
        return $query->count();
    }

    // Query Scope que devuelve el Primer registro del Modelo Elemento
    public function scopePrimerRegistro($query)
    {
        return $query->oldest()->first();
    }

    // Query Scope que devuelve el Último registro del Modelo Elemento
    public function scopeUltimoRegistro($query)
    {
        return $query->latest()->first();
    }

    // Query Scope que devuelve el Total de Registros Eliminados de forma Lógica del Modelo Elemento
    public function scopeTotalEliminadosLogico($query)
    {
        return $query->onlyTrashed()->count();
    }

    // Query Scope que devuelve todos los Registros Eliminados de forma Lógica del Modelo Elemento
    public function scopeEliminadosLogico($query)
    {
        return $query->onlyTrashed();
    }

    // Query Scope que devuelve los registros de una consulta Específica del Modelo Elemento
    public function scopeBusqSelect($query, $atributo, $valorbuscado)
    {
        if (($atributo) && ($valorbuscado))
        {
            return $query->where($atributo,'like',"%$valorbuscado%");
        }
    }

    // Query Scope que devuelve los registros de una consulta General del Modelo Elemento
    public function scopeBusqGeneral($query, $valorbuscado)
    {
        if ($valorbuscado)
        {
            return $query
                ->orwhere('nombreElem','like',"%$valorbuscado%")
                ->orwhere('tipoElem','like',"%$valorbuscado%");
        }
    }
    //SECCIÓN DE QUERY SCOPE

}

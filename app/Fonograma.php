<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class Fonograma extends Model
{
    // Método de Eliminación Lógica del Modelo Empresa
    use SoftDeletes;


    //SECCIÓN DE FILLABLE
    // Atributos del Modelo Fonograma
    protected $fillable = [
        'codigFong',  // Código Fonograma [FON0000]
        'tituloFong',  // Título Fonograma
        'portadillaFong',  // Url del Archivo de Portada del Fonograma
        'añoFong',
        'clasficacionFong',  // Clasificación Fonograma *nom
        'duracionFong',  // Duración total del Fonograma (suma de las duraciones de los Tracks asociados)
        'territorioFong',  // Territorio del Fonograma *nom
        'dueñoDerchFong',  // Nombre y Apellidos del Dueño de los Derechos del Fonograma
        'nacioDueñoDerchFong',  // Nacionalidad del Dueño de los Derechos del Fonograma *nom
        'propiedadFong',  // Nacionalidad Dueño Fonograma *nom
        'dirArbolFong',  // Ruta URL del directorio donde se almacena el Fonograma
        'descripEspFong',  // Descripción en Español del Fonograma
        'descripIngFong',  // Descripción en Inglés del Fonograma
    ];
    //SECCIÓN DE FILLABLE

    //SECCIÓN DE PROTECT TABLE
    protected $table = "fonogramas";
    //SECCIÓN DE PROTECT TABLE

    //SECCIÓN DE IMÁGENES
    public function setPortadillaFongAttribute($portadillaFong, $codigFong)
    {
        $this->attributes['portadillaFong'] = '/BisMusic/Imagenes/Fonogramas/' . $codigFong . $portadillaFong->getClientOriginalName();
        $name = $codigFong . $portadillaFong->getClientOriginalName();
        Storage::disk('local')->put('/Imagenes/Fonogramas/' . $name, File::get($portadillaFong));
    }

    public function setPortadillaFongAttributeDefault()
    {
        $this->attributes['portadillaFong'] = '/BisMusic/Imagenes/Logo ver vertical_Ltr Negras.png';
    }
    //SECCIÓN DE IMÁGENES

    //SECCIÓN DE RELACIONES
    //Relación de One to Many Fonogramas - Elementos
    public function elementos()
    {
        return $this->hasMany(Elemento::class); // Un Fonograma tiene muchos Elementos
    }

    //Relación de Many to Many Fonogramas - Productos
    public function productos()
    {
        return $this->belongsToMany(Producto::class,'producto_fonograma'); // Un Producto tiene muchos Fonogramas
    }
    //Relación de Many to Many Fonogramas - Tracks
    public function tracks()
    {
        return $this->belongsToMany(Track::class,'fonograma_track'); // Un Fonograma tiene muchos Tracks
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
        if (($atributo) && ($valorbuscado)) {
            return $query->where($atributo, 'like', "%$valorbuscado%");
        }
    }

    // Query Scope que devuelve los registros de una consulta General del Modelo Fonograma
    public function scopeBusqGeneral($query, $valorbuscado)
    {
        if ($valorbuscado) {
            return $query
                ->orwhere('codigFong', 'like', "%$valorbuscado%")
                ->orwhere('tituloFong', 'like', "%$valorbuscado%")
                ->orwhere('clasficacionFong', 'like', "%$valorbuscado%")
                ->orwhere('territorioFong', 'like', "%$valorbuscado%")
                ->orwhere('dueñoDerchFong', 'like', "%$valorbuscado%")
                ->orwhere('nacioDueñoDerchFong', 'like', "%$valorbuscado%")
                ->orwhere('propiedadFong', 'like', "%$valorbuscado%");
        }
    }
}

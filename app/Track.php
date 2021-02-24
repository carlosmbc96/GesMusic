<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Track extends Model
{
    // Método de Eliminación Lógica del Modelo Empresa
    use SoftDeletes;


    //SECCIÓN DE FILLABLE
    // Atributos del Modelo Track
    protected $fillable = [
        'tituloTrk',  // Titulo Track*
        'isrcTrk',  // ISRC Track*
        'duracionTrk',  // Duración Track*
        'muestraTrk',  // ¿El Track tiene una pista de Muestra?
        'envivoTrk',  // ¿El Track se Grabó en Vivo?
        'generoTrk',  // Género Musical Track *nom*
        'subgeneroTrk',  // Subgénero Musical Track *nom*
        'bonusTrk',  // ¿Es un bonus Track?
        'moodTrk',  // Estados de Ánimos que refiere el Track *nom*
        'gestionTrk',  // Gestión Track *nom
        'paisgrabTrk',  // País grabación Track *nom*
        'archivoTrk',  // Url del Archivo Track
        'archivoMuestraTrk',  // Url del Archivo de MuestraTrack
        'dirArbolTrk',  // Ruta URL del directorio donde se almacena el Track
    ];
    //SECCIÓN DE FILLABLE

    //SECCIÓN DE PROTECT TABLE
    protected $table = "tracks" ;
    //SECCIÓN DE PROTECT TABLE

    //SECCIÓN DE RELACIONES
    //Relación de One to Many Tracks - Temas
    public function temas()
    {
        return $this->hasMany(Tema::class); // Un Track tiene muchos Temas
    }
    //Relación de Many to Many Fonogramas - Tracks
    public function fonogramas()
    {
        return $this->belongsToMany(Fonograma::class,'fonograma_track'); // Un Fonograma tiene muchos Tracks
    }
    public function autores()
    {
        return $this->belongsToMany(Autor::class,'track_autor'); // Un Fonograma tiene muchos Tracks
    }
    public function interpretes()
    {
        return $this->belongsToMany(Interprete::class,'track_interprete'); // Un Fonograma tiene muchos Tracks
    }
    //SECCIÓN DE RELACIONES


    //SECCIÓN DE QUERY SCOPE
    // Query Scope que devuelve el Total de Registros del Modelo Track
    public function scopeTotalRegistros($query)
    {
        return $query->count();
    }

    // Query Scope que devuelve el Primer registro del Modelo Track
    public function scopePrimerRegistro($query)
    {
        return $query->oldest()->first();
    }

    // Query Scope que devuelve el Último registro del Modelo Track
    public function scopeUltimoRegistro($query)
    {
        return $query->latest()->first();
    }

    // Query Scope que devuelve el Total de Registros Eliminados de forma Lógica del Modelo Track
    public function scopeTotalEliminadosLogico($query)
    {
        return $query->onlyTrashed()->count();
    }

    // Query Scope que devuelve todos los Registros Eliminados de forma Lógica del Modelo Track
    public function scopeEliminadosLogico($query)
    {
        return $query->onlyTrashed();
    }

    // Query Scope que devuelve los registros de una consulta Específica del Modelo Track
    public function scopeBusqSelect($query, $atributo, $valorbuscado)
    {
        if (($atributo) && ($valorbuscado))
        {
            return $query->where($atributo,'like',"%$valorbuscado%");
        }
    }

    // Query Scope que devuelve los registros de una consulta General del Modelo Track
    public function scopeBusqGeneral($query, $valorbuscado)
    {
        if ($valorbuscado)
        {
            return $query
                ->orwhere('tituloTrk','like',"%$valorbuscado%")
                ->orwhere('isrcTrk','like',"%$valorbuscado%")
                ->orwhere('envivoTrk','like',"%$valorbuscado%")
                ->orwhere('generoTrk','like',"%$valorbuscado%")
                ->orwhere('subgeneroTrk','like',"%$valorbuscado%")
                ->orwhere('moodTrk','like',"%$valorbuscado%")
                ->orwhere('gestionTrk','like',"%$valorbuscado%")
                ->orwhere('paisgrabTrk','like',"%$valorbuscado%");
        }
    }
    //SECCIÓN DE QUERY SCOPE

}

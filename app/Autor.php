<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class Autor extends Model
{
    // Método de Eliminación Lógica del Modelo Empresa
    use SoftDeletes;



    //SECCIÓN DE FILLABLE
    // Atributos del Modelo Autor
    protected $fillable = [
        'ciAutr',  // Número Identidad Autor
        'codigAutr',  // Código Autor
        'fotoAutr',  // Url del Archivo de Foto Autor
        'nombresAutr',  // Nombres Autor
        'apellidosAutr',  // Apellidos Autor
        'sexoAutr',  // Sexo Autor *nom
        'fallecidoAutr',  // ¿El Autor es Fallecido?
        'obrasCatEditAutr',  // ¿El Autor tiene Obras en el Catalgo Editorial de Bismusic?
        'reseñaBiogAutr',  // Reseña Biográfica Autor
    ];
    //SECCIÓN DE FILLABLE

    //SECCIÓN DE PROTECT TABLE
    protected $table = "autores";
    //SECCIÓN DE PROTECT TABLE

    //SECCIÓN DE IMÁGENES
    public function setFotoAutrAttribute($fotoAutr, $codigAutr)
    {
        $this->attributes['fotoAutr'] = '/BisMusic/Imagenes/Artistas/Autores/' . $codigAutr . $fotoAutr->getClientOriginalName();
        $name = $codigAutr . $fotoAutr->getClientOriginalName();
        Storage::disk('local')->put('Imagenes/Artistas/Autores/'.$name, File::get($fotoAutr));
    }

    public function setFotoAutrAttributeDefault()
    {
        $this->attributes['fotoAutr'] = '/BisMusic/Imagenes/Logo ver vertical_Ltr Negras.png';
    }
    //SECCIÓN DE IMÁGENES

    //SECCIÓN DE QUERY SCOPE
    // Query Scope que devuelve el Total de Registros del Modelo Autor
    public function scopeTotalRegistros($query)
    {
        return $query->count();
    }
    // Query Scope que devuelve el Primer registro del Modelo Autor
    public function scopePrimerRegistro($query)
    {
        return $query->oldest()->first();
    }

    // Query Scope que devuelve el Último registro del Modelo Autor
    public function scopeUltimoRegistro($query)
    {
        return $query->latest()->first();
    }

    // Query Scope que devuelve el Total de Registros Eliminados de forma Lógica del Modelo Autor
    public function scopeTotalEliminadosLogico($query)
    {
        return $query->onlyTrashed()->count();
    }

    // Query Scope que devuelve todos los Registros Eliminados de forma Lógica del Modelo Autor
    public function scopeEliminadosLogico($query)
    {
        return $query->onlyTrashed();
    }

    // Query Scope que devuelve los registros de una consulta Específica del Modelo Autor
    public function scopeBusqSelect($query, $atributo, $valorbuscado)
    {
        if (($atributo) && ($valorbuscado)) {
            return $query->where($atributo, 'like', "%$valorbuscado%");
        }
    }

    // Query Scope que devuelve los registros de una consulta General del Modelo Autor
    public function scopeBusqGeneral($query, $valorbuscado)
    {
        if ($valorbuscado) {
            return $query
                ->orwhere('ciAutr', 'like', "%$valorbuscado%")
                ->orwhere('codigAutr', 'like', "%$valorbuscado%")
                ->orwhere('nombresAutr', 'like', "%$valorbuscado%")
                ->orwhere('apellidosAutr', 'like', "%$valorbuscado%")
                ->orwhere('sexoAutr', 'like', "%$valorbuscado%")
                ->orwhere('fallecidoAutr', 'like', "%$valorbuscado%");
        }
    }
    //SECCIÓN DE QUERY SCOPE

		public function audiovisuales()
    {
        return $this->belongsToMany(Audiovisuales::class,'audiovisual_autor'); // Un Audiovisual tiene muchos Realizadores
    }

}

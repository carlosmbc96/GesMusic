<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class Audiovisual extends Model
{
    // Método de Eliminación Lógica del Modelo Empresa
    use SoftDeletes;


    //SECCIÓN DE FILLABLE
    // Atributos del Modelo Audiovisual
    protected $fillable = [
        'codigAud',  // Código Audiovisual [AUD0000]
        'isrcAud',  // ISRC Audiovisual
        'tituloAud',  // Título Audiovisual
        'portadillaAud',  // Url del Archivo de Portada del Audiovisual
        'clasifAud',  // Clasificación Audiovisual *nom
        'generoAud',  // Género Audiovisual *nom
        'duracionAud',  // Duración Audiovisual
        'añoFinAud',  // Año que se terminó el Audiovisual *nom
        'paisGrabAud',  // País de Grabación del Audiovisual *nom
        'territorioAud',  // Territorio del Audiovisual
        'idiomaAud',  // Idioma Audiovisual *nom
        'subtitulosAud',  // Subtitulos Audiovisual *nom
        'fenomRefAud',  // Fenómeno de Referencia del Audiovisual
        'etiquetasAud',  // Etiquetas del Audiovisual *nom
        'archivoAud',  // Url del Archivo Audiovisual Media
        'dueñoDerchAud',  // Nombre y Apellidos del Dueño de los Derechos del Audivisual
        'nacioDueñoDerchAud',  // Nacionalidad del Dueño de los Derechos del Audivisual
        'derechosAud',  // Nacionalidad Dueño Producto *nom
        'makingOfAud',  // ¿El Audiovisual tiene Making-Of?
        'dirArbolAud',  // Ruta URL del directorio donde se almacena el Audiovisual
        'descripEspAud',  // Descripción en Español Media
        'descripIngAud',  // Descripción en Inglés Media
    ];
    //SECCIÓN DE FILLABLE

    //SECCIÓN DE PROTECT TABLE
    protected $table = "audiovisuales";
    //SECCIÓN DE PROTECT TABLE

    //SECCIÓN DE IMÁGENES
    public function setPortadillaAudAttribute($portadillaAud, $codigAud)
    {
        $this->attributes['portadillaAud'] = '/BisMusic/Imagenes/Audiovisuales/' . $codigAud . $portadillaAud->getClientOriginalName();
        $name = $codigAud . $portadillaAud->getClientOriginalName();
        Storage::disk('local')->put('/Imagenes/Audiovisuales/' . $name, File::get($portadillaAud));
    }

    public function setPortadillaAudAttributeDefault()
    {
        $this->attributes['portadillaAud'] = '/BisMusic/Imagenes/Logo ver vertical_Ltr Negras.png';
    }
    //SECCIÓN DE IMÁGENES

    //SECCIÓN DE RELACIONES
    //Relación de Many to Many Audiovisuales - Productos
    public function productos()
    {
        return $this->belongsToMany(Producto::class,'producto_audiovisual'); // Un Producto tiene muchos Fonogramas
    }
    //Relación de One to Many Audiovisuales - Elementos
    public function elementos()
    {
        return $this->hasMany(Elemento::class); // Un Audiovisual tiene muchos Elementos
    }
    //Relación de One to Many Audiovisuales - Entrevistados
    public function entrevistados()
    {
        return $this->hasMany(Entrevistado::class); // Un Audiovisual tiene muchos Entrevistados
    }
    //Relación de One to Many Audiovisuales - Realizadores
    public function realizadores()
    {
        return $this->hasMany(Realizador::class); // Un Audiovisual tiene muchos Realizadores
    }
    //SECCIÓN DE RELACIONES


    //SECCIÓN DE QUERY SCOPE
    // Query Scope que devuelve el Total de Registros del Modelo Audiovisual
    public function scopeTotalRegistros($query)
    {
        return $query->count();
    }

    // Query Scope que devuelve el Primer registro del Modelo Audiovisual
    public function scopePrimerRegistro($query)
    {
        return $query->oldest()->first();
    }

    // Query Scope que devuelve el Último registro del Modelo Audiovisual
    public function scopeUltimoRegistro($query)
    {
        return $query->latest()->first();
    }

    // Query Scope que devuelve el Total de Registros Eliminados de forma Lógica del Modelo Audiovisual
    public function scopeTotalEliminadosLogico($query)
    {
        return $query->onlyTrashed()->count();
    }

    // Query Scope que devuelve todos los Registros Eliminados de forma Lógica del Modelo Audiovisual
    public function scopeEliminadosLogico($query)
    {
        return $query->onlyTrashed();
    }

    // Query Scope que devuelve los registros de una consulta Específica del Modelo Audiovisual
    public function scopeBusqSelect($query, $atributo, $valorbuscado)
    {
        if (($atributo) && ($valorbuscado)) {
            return $query->where($atributo, 'like', "%$valorbuscado%");
        }
    }

    // Query Scope que devuelve los registros de una consulta General del Modelo Audiovisual
    public function scopeBusqGeneral($query, $valorbuscado)
    {
        if ($valorbuscado) {
            return $query
                ->orwhere('codigAud', 'like', "%$valorbuscado%")
                ->orwhere('isrcAud', 'like', "%$valorbuscado%")
                ->orwhere('tituloAud', 'like', "%$valorbuscado%")
                ->orwhere('clasifAud', 'like', "%$valorbuscado%")
                ->orwhere('generoAud', 'like', "%$valorbuscado%")
                ->orwhere('añoFinAud', 'like', "%$valorbuscado%")
                ->orwhere('paisGrabAud', 'like', "%$valorbuscado%")
                ->orwhere('idiomaAud', 'like', "%$valorbuscado%")
                ->orwhere('propiedadAud', 'like', "%$valorbuscado%");
        }
    }
    //SECCIÓN DE QUERY SCOPE
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class Producto extends Model
{
    // Método de Eliminación Lógica del Modelo Empresa
    use SoftDeletes;

    //SECCIÓN DE FILLABLE
    // Atributos del Modelo Producto
    protected $fillable = [
        'codigProd',  // Código Producto [B0000]
        'tituloProd',  // Título Producto
        'añoProd',  // Año Producto *nom
        'portadillaProd',  // Url del Archivo de Foto Portada Producto
        'sellodiscProd',  // Sello Discográfico del Producto *nom
        'estadodigProd',  // Estado de Digitalización Producto *nom
        'statusComProd',  // Estatud Comercial Producto *nom
        'destinosComerPro',  // Destino Comercial *nom
        'producPrincProd',  // ¿Producto Principal del Proyecto?
        'codigBarProd',  // Código de Barra Producto
        'genMusicPro',  // Género Musical Producto *nom
        'activoCatalbisPro',  // ¿Activo en el Catalogo de Bismusic el Producto?
        'catalDigitalPro',  // ¿Estará el Producto en el Catalogo Digital?
        'primeraPantProd',  // ¿Estará el Producto en la Primera Pantalla?
        'variosInterpretesProd',  // ¿El Producto tiene varios Intérpretes?
        'interpretesProd',  // Intérpretes del Producto
        'autoresProd',  // Autores del Producto
        'dirArbolProd',  // Ruta URL del directorio donde se almacena el Producto
        'descripEspPro',  // Descripción Español Producto
        'descripIngPro',  // Descripción Inglés Producto
        'identificadorProd',  // Identificador del Producto

        'proyecto_id',  // Identificador Único de la tabla Proyectos  / Relación One to Many
    ];
    //SECCIÓN DE FILLABLE

    //SECCIÓN DE IMÁGENES
    public function setIdentificadorProdAttribute($identificadorProd, $codigProd)
    {
        if ($identificadorProd) {
            $this->attributes['identificadorProd'] = '/BisMusic/Imagenes/Productos/' . $codigProd . $identificadorProd->getClientOriginalName();
            $name = $codigProd . $identificadorProd->getClientOriginalName();
            Storage::disk('local')->put('Imagenes/Productos/'.$name, File::get($identificadorProd));
        }
    }
    public function setIdentificadorProdAttributeDefault()
    {
        $this->attributes['identificadorProd'] = '/BisMusic/Imagenes/Logo ver vertical_Ltr Negras.png';
    }
    //SECCIÓN DE IMÁGENES

    //SECCIÓN DE PROTECT TABLE
    protected $table = "productos";
    //SECCIÓN DE PROTECT TABLE

    //SECCIÓN DE RELACIONES
    //Relación de Many to One Productos - Proyectos
    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class); // Un Proyecto pertenece a una Empresa
    }

    //Relación de One to Many Productos - Elementos
    public function elementos()
    {
        return $this->hasMany(Elemento::class); // Un Producto tiene muchos Elementos
    }
    //Relación de Many to Many Productos - Fonogramas
    public function fonogramas()
    {
        return $this->belongsToMany(Fonograma::class,'producto_fonograma'); // Un Producto tiene muchos Fonogramas
    }
    //Relación de Many to Many Productos - Fonogramas
    public function audiovisuales()
    {
        return $this->belongsToMany(Audiovisual::class,'producto_audiovisual'); // Un Producto tiene muchos Fonogramas
    }
    //SECCIÓN DE RELACIONES


    //SECCIÓN DE QUERY SCOPE
    // Query Scope que devuelve el Total de Registros del Modelo Proyecto
    public function scopeTotalRegistros($query)
    {
        return $query->count();
    }

    // Query Scope que devuelve el Primer registro del Modelo Proyecto
    public function scopePrimerRegistro($query)
    {
        return $query->oldest()->first();
    }

    // Query Scope que devuelve el Último registro del Modelo Proyecto
    public function scopeUltimoRegistro($query)
    {
        return $query->latest()->first();
    }

    // Query Scope que devuelve el Total de Registros Eliminados de forma Lógica del Modelo Proyecto
    public function scopeTotalEliminadosLogico($query)
    {
        return $query->onlyTrashed()->count();
    }

    // Query Scope que devuelve todos los Registros Eliminados de forma Lógica del Modelo Proyecto
    public function scopeEliminadosLogico($query)
    {
        return $query->onlyTrashed();
    }

    // Query Scope que devuelve los registros de una consulta Específica del Modelo Proyecto
    public function scopeBusqSelect($query, $atributo, $valorbuscado)
    {
        if (($atributo) && ($valorbuscado)) {
            return $query->where($atributo, 'like', "%$valorbuscado%");
        }
    }

    // Query Scope que devuelve los registros de una consulta General del Modelo Producto
    public function scopeBusqGeneral($query, $valorbuscado)
    {
        if ($valorbuscado) {
            return $query
                ->orwhere('codigProd', 'like', "%$valorbuscado%")
                ->orwhere('tituloProd', 'like', "%$valorbuscado%")
                ->orwhere('añoProd', 'like', "%$valorbuscado%")
                ->orwhere('sellodiscProd', 'like', "%$valorbuscado%")
                ->orwhere('estadodigProd', 'like', "%$valorbuscado%")
                ->orwhere('statusComProd', 'like', "%$valorbuscado%")
                ->orwhere('destinosComerPro', 'like', "%$valorbuscado%")
                ->orwhere('genMusicPro', 'like', "%$valorbuscado%")
                ->orwhere('interpretesProd', 'like', "%$valorbuscado%")
                ->orwhere('autoresProd', 'like', "%$valorbuscado%");
        }
    }
    //SECCIÓN DE QUERY SCOPE

}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class Proyecto extends Model
{
    // Método de Eliminación Lógica del Modelo Empresa
    use SoftDeletes;


    //SECCIÓN DE FILLABLE
    protected $fillable = [
        'codigProy',  // Código Proyecto
        'nombreProy',  // Nombre Proyecto
        'añoProy',  // Año del Proyecto
        'identificadorProy',  // Identificador del Proyecto
        'dirArbolProy',  // Ruta URL del directorio donde se almacena el Proyecto
        'descripEsp',  // Descripción Español Proyecto
        'descripIng',  // Descripción Inglés Proyecto

        'empresa_id',  // Identificador Único de la tabla Empresas  / Relación One to Many
    ];
    //SECCIÓN DE FILLABLE

    public function setIdentificadorProyAttribute($identificadorProy)
    {
            $this->attributes['identificadorProy'] = '/BisMusic/Proyectos/' . Carbon::now()->second . $identificadorProy->getClientOriginalName();
            $name = Carbon::now()->second . $identificadorProy->getClientOriginalName();
            Storage::disk('local')->put($name, File::get($identificadorProy));
    }

    public function setIdentificadorProyAttributeDefault()
    {
        $this->attributes['identificadorProy'] = '/BisMusic/Proyectos/Logo ver vertical_Ltr Negras.png';
    }

    //SECCIÓN DE RELACIONES
    public function empresa()
    {
        return $this->belongsTo(Empresa::class); // Un Proyecto pertenece a una Empresa
    }
    //Relación de One to Many Proyectos - Productos
    public function productos()
    {
        return $this->hasMany(Producto::class); // Una Empresa tiene muchos Proyectos
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

    // Query Scope que devuelve los registros de una consulta General del Modelo Proyecto
    public function scopeBusqGeneral($query, $valorbuscado)
    {
        if ($valorbuscado) {
            return $query
                ->orwhere('codigProy', 'like', "%$valorbuscado%")
                ->orwhere('añoProy', 'like', "%$valorbuscado%")
                ->orwhere('nombreProy', 'like', "%$valorbuscado%");
        }
    }
    //SECCIÓN DE QUERY SCOPE

}

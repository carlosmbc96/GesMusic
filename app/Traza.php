<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Traza extends Model
{
    // Método de Eliminación Lógica del Modelo Empresa
    use SoftDeletes;


    //SECCIÓN DE FILLABLE
    // Atributos del Modelo Traza
    protected $fillable = [
        'nombreUser',  // Nombre Usuario Traza
        'ipDireccion',  // Dirección IP genera Traza
        'url',  // URL de la Traza
        'usuarioAgent',  // Agente del Usuario
        'valorAntg',  // Valor Antiguo Traza
        'ValorNue',  // Valor Nuevo Traza

        'empleado_id',  // Identificador Único de la tabla Empleado  / Relación One to Many
    ];
    //SECCIÓN DE FILLABLE

    //SECCIÓN DE PROTECT TABLE
    protected $table = "trazas" ;
    //SECCIÓN DE PROTECT TABLE

    //SECCIÓN DE RELACIONES
    //Relación de Many to One Trazas - Usuarios
    public function usuario()
    {
        return $this->belongsTo(Usuario::class); // Una Traza esta asociado a un Usuario
    }
    //SECCIÓN DE RELACIONES


    //SECCIÓN DE QUERY SCOPE
    // Query Scope que devuelve el Total de Registros del Modelo Traza
    public function scopeTotalRegistros($query)
    {
        return $query->count();
    }

    // Query Scope que devuelve el Primer registro del Modelo Traza
    public function scopePrimerRegistro($query)
    {
        return $query->oldest()->first();
    }

    // Query Scope que devuelve el Último registro del Modelo Traza
    public function scopeUltimoRegistro($query)
    {
        return $query->latest()->first();
    }

    // Query Scope que devuelve el Total de Registros Eliminados de forma Lógica del Modelo Traza
    public function scopeTotalEliminadosLogico($query)
    {
        return $query->onlyTrashed()->count();
    }

    // Query Scope que devuelve todos los Registros Eliminados de forma Lógica del Modelo Traza
    public function scopeEliminadosLogico($query)
    {
        return $query->onlyTrashed();
    }

    // Query Scope que devuelve los registros de una consulta Específica del Modelo Traza
    public function scopeBusqSelect($query, $atributo, $valorbuscado)
    {
        if (($atributo) && ($valorbuscado))
        {
            return $query->where($atributo,'like',"%$valorbuscado%");
        }
    }

    // Query Scope que devuelve los registros de una consulta General del Modelo Traza
    public function scopeBusqGeneral($query, $valorbuscado)
    {
        if ($valorbuscado)
        {
            return $query
                ->orwhere('nombreUser','like',"%$valorbuscado%")
                ->orwhere('ipDireccion','like',"%$valorbuscado%")
                ->orwhere('url','like',"%$valorbuscado%");
        }
    }
    //SECCIÓN DE QUERY SCOPE

}

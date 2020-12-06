<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Usuario extends Model
{
    // Método de Eliminación Lógica del Modelo Empresa
    use SoftDeletes;


    //SECCIÓN DE FILLABLE
    // Atributos del Modelo Usuario
    protected $fillable = [
        'estadoUser',  // Estado Gestión Usuario *nom
        'nombreUser',  // Nombre Usuario
        'emailUser',  // Correo del Usuario
        'verificaEmailUser',  // Verifica Correo del Usuario
        'contraseñaUser',  // Contraseña Usuario Empleado
        'fechaCaduc',  // Fecha Caducidad Contraseña Usuario Empleado

        'empleado_id',  // Identificador Único de la tabla Empresas  / Relación One to Many
    ];
    //SECCIÓN DE FILLABLE


    //SECCIÓN DE RELACIONES
    //Relación de One to Many Usuarios - Trazas
    public function trazas()
    {
        return $this->hasMany(Traza::class); // Un Usuarios tiene muchas Trazas
    }
    //Relación de One to One Usuarios - Empleados
    public function empleados()
    {
        return $this->belongsTo(Empleado::class); // Un Empleado tiene un Usuario asociado
    }
    //SECCIÓN DE RELACIONES


    //SECCIÓN DE QUERY SCOPE
    // Query Scope que devuelve el Total de Registros del Modelo Usuario
    public function scopeTotalRegistros($query)
    {
        return $query->count();
    }

    // Query Scope que devuelve el Primer registro del Modelo Usuario
    public function scopePrimerRegistro($query)
    {
        return $query->oldest()->first();
    }

    // Query Scope que devuelve el Último registro del Modelo Usuario
    public function scopeUltimoRegistro($query)
    {
        return $query->latest()->first();
    }

    // Query Scope que devuelve el Total de Registros Eliminados de forma Lógica del Modelo Usuario
    public function scopeTotalEliminadosLogico($query)
    {
        return $query->onlyTrashed()->count();
    }

    // Query Scope que devuelve todos los Registros Eliminados de forma Lógica del Modelo Usuario
    public function scopeEliminadosLogico($query)
    {
        return $query->onlyTrashed();
    }

    // Query Scope que devuelve los registros de una consulta Específica del Modelo Usuario
    public function scopeBusqSelect($query, $atributo, $valorbuscado)
    {
        if (($atributo) && ($valorbuscado))
        {
            return $query->where($atributo,'like',"%$valorbuscado%");
        }
    }

    // Query Scope que devuelve los registros de una consulta General del Modelo Usuario
    public function scopeBusqGeneral($query, $valorbuscado)
    {
        if ($valorbuscado)
        {
            return $query
                ->orwhere('estadoUser','like',"%$valorbuscado%")
                ->orwhere('nombreUser','like',"%$valorbuscado%")
                ->orwhere('emailUser','like',"%$valorbuscado%")
                ->orwhere('fechaCaduc','like',"%$valorbuscado%");
        }
    }
    //SECCIÓN DE QUERY SCOPE

}
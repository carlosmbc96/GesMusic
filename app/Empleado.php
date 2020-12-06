<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Empleado extends Model
{
    // Método de Eliminación Lógica del Modelo Empresa
    use SoftDeletes;


    //SECCIÓN DE FILLABLE
    // Atributos del Modelo Empleado
    protected $fillable = [
        'ciEmpl',  // Número Identidad Empleado
        'fotoEmpl',  // Url del Archivo de Foto Empleado
        'nombresEmpl',  // Nombres Empleado
        'apellidosEmpl',  // Apellidos Empleado
        'sexoEmpl',  // Sexo Empleado *nom
        'fechaNacEmpl',  // Fecha Nacimiento Empleado
        'empresaEmpl',  // Empresa Empleado
        'cargoEmpl',  // Cargo Empleado
        'DptoEmpl',  // Departamento Empleado
        'direccionEmpl',  // Direción de Residencia Empleado
        'celEmpl',  // Móvil Empleado
        'telfEmpl',  // Teléfono Fijo Empleado
        'emailEmpl',  // Correo Electrónico Empleado
        'webSocialEmpl',  // Url Web Social Empleado
        'descripEmpl',  // Descripción Empleado
    ];
    //SECCIÓN DE FILLABLE


    //SECCIÓN DE RELACIONES
    //Relación de One to One Empleados - Usuarios
    public function usuarios()
    {
        return $this->hasOne(Usuario::class); // Un Usuarios tiene un Empleado asociado
    }
    //Relación de Many to One Temas - Tracks
    public function empresas()
    {
        return $this->belongsTo(Empresa::class); // Un Empleado esta asociado a una Empresa
    }
    //SECCIÓN DE RELACIONES



    //SECCIÓN DE QUERY SCOPE
    // Query Scope que devuelve el Total de Registros del Modelo Empleado
    public function scopeTotalRegistros($query)
    {
        return $query->count();
    }

    // Query Scope que devuelve el Primer registro del Modelo Empleado
    public function scopePrimerRegistro($query)
    {
        return $query->oldest()->first();
    }

    // Query Scope que devuelve el Último registro del Modelo Empleado
    public function scopeUltimoRegistro($query)
    {
        return $query->latest()->first();
    }

    // Query Scope que devuelve el Total de Registros Eliminados de forma Lógica del Modelo Empleado
    public function scopeTotalEliminadosLogico($query)
    {
        return $query->onlyTrashed()->count();
    }

    // Query Scope que devuelve todos los Registros Eliminados de forma Lógica del Modelo Empleado
    public function scopeEliminadosLogico($query)
    {
        return $query->onlyTrashed();
    }

    // Query Scope que devuelve los registros de una consulta Específica del Modelo Empleado
    public function scopeBusqSelect($query, $atributo, $valorbuscado)
    {
        if (($atributo) && ($valorbuscado))
        {
            return $query->where($atributo,'like',"%$valorbuscado%");
        }
    }

    // Query Scope que devuelve los registros de una consulta General del Modelo Empleado
    public function scopeBusqGeneral($query, $valorbuscado)
    {
        if ($valorbuscado)
        {
            return $query
                ->orwhere('ciEmpl','like',"%$valorbuscado%")
                ->orwhere('nombresEmpl','like',"%$valorbuscado%")
                ->orwhere('apellidosEmpl','like',"%$valorbuscado%")
                ->orwhere('sexoEmpl','like',"%$valorbuscado%")
                ->orwhere('cargoEmpl','like',"%$valorbuscado%")
                ->orwhere('cargoEmpl','like',"%$valorbuscado%");
        }
    }
    //SECCIÓN DE QUERY SCOPE

}

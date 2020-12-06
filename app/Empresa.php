<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Empresa extends Model
{
    // Método de Eliminación Lógica del Modelo Empresa
    use SoftDeletes;


    //SECCIÓN DE FILLABLE
    // Atributos del Modelo Empresa
    protected $fillable = [
        'codEmpr',  // Código Empresa
        'logoEmpr',  // Url del Archivo de Logo Empresa
        'nombreEmpr',  // Nombre Empresa
        'siglasEmpr',  // Siglas Empresa
        'tipoEmpr',  // Tipo Empresa *nom
        'persJurEmpr',  // Pers. Juridica Empresa *nom
        'fechaCreaEmpr',  // Fecha Creación Empresa
        'direccionEmpr',  // Dirección Legal Empresa
        'telFijo1Empr',  // Teléfono Fijo # 1 Empresa
        'telFijo2Empr',  // Teléfono Fijo # 2 Empresa
        'email1Empr',  // Correo # 1 Empresa
        'email2Empr',  // Correo # 2 Empresa
        'webOficialEmpr',  // Web Oficial Empresa
        'socialWeb1Empr',  // Web Social # 1 Empresa
        'socialWeb2Empr',  // Web Social # 2 Empresa
        'socialWeb3Empr',  // Web Social # 3 Empresa
        'socialWeb4Empr',  // Web Social # 4 Empresa
        'descripEmpr',  // Descripción Empresa
        'otrosDatosEmpr',  // Otros Datos Empresa
    ];
    //SECCIÓN DE FILLABLE


    //SECCIÓN DE RELACIONES
    //Relación de One to Many Empresas - Proyectos
    public function proyectos()
    {
        return $this->hasMany(Proyecto::class); // Una Empresa tiene muchos Proyectos
    }
    //Relación de One to Many Empresas - Empleados
    public function empleados()
    {
        return $this->hasMany(Empleado::class); // Una Empresa tiene muchos Empleados
    }
    //SECCIÓN DE RELACIONES


    //SECCIÓN DE QUERY SCOPE
    // Query Scope que devuelve el Total de Registros del Modelo Empresa
    public function scopeTotalRegistros($query)
    {
        return $query->count();
    }

    // Query Scope que devuelve el Primer registro del Modelo Empresa
    public function scopePrimerRegistro($query)
    {
        return $query->oldest()->first();
    }

    // Query Scope que devuelve el Último registro del Modelo Empresa
    public function scopeUltimoRegistro($query)
    {
        return $query->latest()->first();
    }

    // Query Scope que devuelve el Total de Registros Eliminados de forma Lógica del Modelo Empresa
    public function scopeTotalEliminadosLogico($query)
    {
        return $query->onlyTrashed()->count();
    }

    // Query Scope que devuelve todos los Registros Eliminados de forma Lógica del Modelo Empresa
    public function scopeEliminadosLogico($query)
    {
        return $query->onlyTrashed();
    }

    // Query Scope que devuelve los registros de una consulta Específica del Modelo Empresa
    public function scopeBusqSelect($query, $atributo, $valorbuscado)
    {
        if (($atributo) && ($valorbuscado))
        {
            return $query->where($atributo,'like',"%$valorbuscado%");
        }
    }

    // Query Scope que devuelve los registros de una consulta General del Modelo Empresa
    public function scopeBusqGeneral($query, $valorbuscado)
    {
        if ($valorbuscado)
        {
            return $query
                ->orwhere('nombreInterp','like',"%$valorbuscado%")
                ->orwhere('reseñaBiogInterp','like',"%$valorbuscado%");
        }
    }
    //SECCIÓN DE QUERY SCOPE

}

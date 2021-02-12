<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Entrevistado extends Model
{
	// Método de Eliminación Lógica del Modelo Empresa
	use SoftDeletes;


	//SECCIÓN DE FILLABLE
	// Atributos del Modelo Entrevistado
	protected $fillable = [
		'codigEntrv',
		'nombreApellidosEntrv',  // Nombre y Apellidos del Entrevistado
		'sexoEntrv',  // Sexo del Entrevistado
		'descripEspEntrv',  // Descripción en Español del Entrevistado
	];
	//SECCIÓN DE FILLABLE

	//SECCIÓN DE PROTECT TABLE
	protected $table = "entrevistados";
	//SECCIÓN DE PROTECT TABLE

	//SECCIÓN DE RELACIONES
	//Relación de Many to One Entrevistados - Audiovisuales
	public function audiovisuales()
	{
		return $this->belongsToMany(Audiovisual::class,'audiovisual_entrevistado');
	}
	//SECCIÓN DE RELACIONES


	//SECCIÓN DE QUERY SCOPE
	// Query Scope que devuelve el Total de Registros del Modelo Entrevistado
	public function scopeTotalRegistros($query)
	{
		return $query->count();
	}

	// Query Scope que devuelve el Primer registro del Modelo Entrevistado
	public function scopePrimerRegistro($query)
	{
		return $query->oldest()->first();
	}

	// Query Scope que devuelve el Último registro del Modelo Entrevistado
	public function scopeUltimoRegistro($query)
	{
		return $query->latest()->first();
	}

	// Query Scope que devuelve el Total de Registros Eliminados de forma Lógica del Modelo Entrevistado
	public function scopeTotalEliminadosLogico($query)
	{
		return $query->onlyTrashed()->count();
	}

	// Query Scope que devuelve todos los Registros Eliminados de forma Lógica del Modelo Entrevistado
	public function scopeEliminadosLogico($query)
	{
		return $query->onlyTrashed();
	}

	// Query Scope que devuelve los registros de una consulta Específica del Modelo Entrevistado
	public function scopeBusqSelect($query, $atributo, $valorbuscado)
	{
		if (($atributo) && ($valorbuscado)) {
			return $query->where($atributo, 'like', "%$valorbuscado%");
		}
	}

	// Query Scope que devuelve los registros de una consulta General del Modelo Entrevistado
	public function scopeBusqGeneral($query, $valorbuscado)
	{
		if ($valorbuscado) {
			return $query
				->orwhere('codigEntrv', 'like', "%$valorbuscado%")
				->orwhere('nombreApellidosEntrv', 'like', "%$valorbuscado%")
				->orwhere('sexoEntrv', 'like', "%$valorbuscado%");
		}
	}
	//SECCIÓN DE QUERY SCOPE


}

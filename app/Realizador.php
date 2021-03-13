<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class Realizador extends Model
{
	// Método de Eliminación Lógica del Modelo Empresa
	use SoftDeletes;


	//SECCIÓN DE FILLABLE
	// Atributos del Modelo Realizador
	protected $fillable = [
		'codigRealiz',
		'fotoReal',
		'nombreApellidosRealiz',  // Nombre y Apellidos del Realizador
		'sexoRealiz',  // Sexo Realizador *nom
		'descripEspRealiz',  // Descripción en Español del Realizador
	];
	//SECCIÓN DE FILLABLE

	//SECCIÓN DE PROTECT TABLE
	protected $table = "realizadores";
	//SECCIÓN DE PROTECT TABLE

	//SECCIÓN DE RELACIONES
	//Relación de Many to One Realizadores - Audiovisuales
	public function audiovisuales()
	{
		return $this->belongsToMany(Audiovisual::class, 'audiovisual_realizador');
	}
	//SECCIÓN DE RELACIONES

	//SECCIÓN DE IMÁGENES
	public function setFotoRealAttribute($fotoReal, $codigRealiz)
	{
		$this->attributes['fotoReal'] = '/BisMusic/Imagenes/Realizadores/' . $codigRealiz . $fotoReal->getClientOriginalName();
		$name = $codigRealiz . $fotoReal->getClientOriginalName();
		Storage::disk('local')->put('Imagenes/Realizadores/' . $name, File::get($fotoReal));
	}

	public function setFotoRealAttributeDefault()
	{
		$this->attributes['fotoReal'] = '/BisMusic/Imagenes/Logo ver vertical_Ltr Negras.png';
	}
	//SECCIÓN DE IMÁGENES

	//SECCIÓN DE QUERY SCOPE
	// Query Scope que devuelve el Total de Registros del Modelo Realizador
	public function scopeTotalRegistros($query)
	{
		return $query->count();
	}

	// Query Scope que devuelve el Primer registro del Modelo Realizador
	public function scopePrimerRegistro($query)
	{
		return $query->oldest()->first();
	}

	// Query Scope que devuelve el Último registro del Modelo Realizador
	public function scopeUltimoRegistro($query)
	{
		return $query->latest()->first();
	}

	// Query Scope que devuelve el Total de Registros Eliminados de forma Lógica del Modelo Realizador
	public function scopeTotalEliminadosLogico($query)
	{
		return $query->onlyTrashed()->count();
	}

	// Query Scope que devuelve todos los Registros Eliminados de forma Lógica del Modelo Realizador
	public function scopeEliminadosLogico($query)
	{
		return $query->onlyTrashed();
	}

	// Query Scope que devuelve los registros de una consulta Específica del Modelo Realizador
	public function scopeBusqSelect($query, $atributo, $valorbuscado)
	{
		if (($atributo) && ($valorbuscado)) {
			return $query->where($atributo, 'like', "%$valorbuscado%");
		}
	}

	// Query Scope que devuelve los registros de una consulta General del Modelo Realizador
	public function scopeBusqGeneral($query, $valorbuscado)
	{
		if ($valorbuscado) {
			return $query
				->orwhere('nombreApellidosRealiz', 'like', "%$valorbuscado%")
				->orwhere('sexoRealiz', 'like', "%$valorbuscado%");
		}
	}
	//SECCIÓN DE QUERY SCOPE

}

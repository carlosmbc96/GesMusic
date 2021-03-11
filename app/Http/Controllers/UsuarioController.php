<?php

namespace App\Http\Controllers;

use App\Usuario;
use App\Vocabulario;
use Illuminate\Http\Request;

class UsuarioController extends Controller
{
	public function index(Request $request)  // Index | Método que lista todos los Registros del Modelo:Usuario
	{
		$valorbuscado = $request->valorbuscado;
		$atributo = $request->atributo;
		if (($atributo) && ($valorbuscado)) {
			$usuarios = Usuario::withTrashed()->BusqSelect($atributo, $valorbuscado)->get();
		} else if ($valorbuscado) {
			$usuarios = Usuario::withTrashed()->BusqGeneral($valorbuscado)->get();
		} else {
			$usuarios = Usuario::withTrashed()->get();
		}
		$relaciones = $request->relations; //  Aqui se accede al objeto relations que viene por parámetros
		//  En este bloque If se verifica que exista un objeto relaciones, en caso de que exista se accede a la primera posición del arreglo, si es all lo que contiene entonces se devuelven todas las relaciones para el modelo en cuestión.
		if ($relaciones) {
			if ($relaciones[0] === 'all') {
				$i = 0;
				$length = count($usuarios);
				for ($i; $i < $length; $i++) {
					$usuarios[$i]->trazas;
					$usuarios[$i]->empleado;
				}
			} else {
				$i = 0;
				$lengthUsuarios = count($usuarios);
				$lengthRelaciones = count($relaciones);
				for ($i; $i < $lengthUsuarios; $i++) {
					for ($j = 0; $j < $lengthRelaciones; $j++) {
						$relacionesMetodo = (string)$relaciones[$j];
						$usuarios[$i]->$relacionesMetodo;
					}
				}
			}
		}
		return response()->json($usuarios);
	}

	public function nomenclators()  // Nomenclators | Método que carga los Nomencladores en el Modelo:Usuario
	{
		// Sección de Carga de Nomencladores a emplear en las vista
		$estdUsua = Vocabulario::findorFail(10)->terminos;  // Nomenclador: Estados Usuarios
		return response()->json($estdUsua);  // Se envian las variables
	}

	public function store(Request $request)  // Store | Método que Guarda el Registro creado en el Modelo:Usuario
	{
		return response()->json(Usuario::create($request->all()));
	}

	public function update(Request $request)  // Update | Método que Actualiza un Registro Específico del Modelo:Usuario
	{
		return response()->json(Usuario::findOrFail($request->id)->update($request->all()));
	}

	public function destroyLog(Usuario $usuario, $id)  // DestroyLog | Método que Elimina de forma Lógica un Registro Específico del Modelo:Usuario
	{
		return response()->json(Usuario::findOrFail($id)->delete());
	}

	public function destroyFis(Usuario $usuario, $id)  // DestroyFis | Método que Elimina de forma Física un Registro Específico del Modelo:Usuario
	{
		return response()->json(Usuario::findOrFail($id)->forceDelete());
	}

	public function restoreLog($id)  // RestoreLog | Método que Restaura un Registro Específico, eliminado de forma Lógica del Modelo:Usuario
	{
		return response()->json(Usuario::onlyTrashed()->findOrFail($id)->restore());
	}
}

<?php

namespace App\Http\Controllers;

use App\Entrevistado;
use App\Vocabulario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EntrevistadoController extends Controller
{
	public function index(Request $request)  // Index | Método que lista todos los Registros del Modelo:Entrevistado
	{
		$valorbuscado = $request->valorbuscado;
		$atributo = $request->atributo;
		if (($atributo) && ($valorbuscado)) {
			$entrevistados = Entrevistado::BusqSelect($atributo, $valorbuscado)->paginate(5);
		} else if ($valorbuscado) {
			$entrevistados = Entrevistado::BusqGeneral($valorbuscado)->paginate(5);
		} else {
			$entrevistados = Entrevistado::withTrashed()->get();
		}
		$relaciones = $request->relations; //  Aqui se accede al objeto relations que viene por parámetros
		//  En este bloque If se verifica que exista un objeto relaciones, en caso de que exista se accede a la primera posición del arreglo, si es all lo que contiene entonces se devuelven todas las relaciones para el modelo en cuestión.
		if ($relaciones) {
			if ($relaciones[0] === 'all') {
				$i = 0;
				$length = count($entrevistados);
				for ($i; $i < $length; $i++) {
					$entrevistados[$i]->audiovisuales;
				}
			} else {
				$i = 0;;
				$lengthEntrevistados = count($entrevistados);
				$lengthRelaciones = count($relaciones);
				for ($i; $i < $lengthEntrevistados; $i++) {
					for ($j = 0; $j < $lengthRelaciones; $j++) {
						$relacionesMetodo = (string)$relaciones[$j];
						$entrevistados[$i]->$relacionesMetodo;
					}
				}
			}
		}
		return response()->json($entrevistados);
	}

	public function nomenclators()  // Nomenclators | Método que carga los Nomencladores en el Modelo:Entrevistado
	{
		// Sección de Carga de Nomencladores a emplear en las vista
		$sexos = Vocabulario::findorFail(28)->terminos;  // Nomenclador: Sexos
		return response()->json($sexos);  // Se envian las variables
	}

	public function store(Request $request)  // Store | Método que Guarda el Registro creado en el Modelo:Entrevistado
	{
		$entrevistado = Entrevistado::create([
			"codigEntrv" => $request->codigEntrv,
			"nombreApellidosEntrv" => $request->nombreApellidosEntrv,
			"sexoEntrv" => $request->sexoEntrv,
			"descripEspEntrv" => $request->descripEspEntrv,
		]);
		if ($request->fotoEntrv !== null) {
			$entrevistado->setFotoEntrvAttribute($request->fotoEntrv, $request->codigEntrv);
		} else
			$entrevistado->setFotoEntrvAttributeDefault();
		$entrevistado->save();
		return response()->json($entrevistado);
	}

	public function update(Request $request)  // Update | Método que Actualiza un Registro Específico del Modelo:Interprete
	{
		$entrevistado = Entrevistado::findOrFail($request->id);
		if ($request->fotoEntrv !== null) {
			if (substr($entrevistado->fotoEntrv, 33) !== "Logo ver vertical_Ltr Negras.png") {
				Storage::disk('local')->delete('/Imagenes/Entrevistados/' . substr($entrevistado->fotoEntrv, 33));
			}
			$entrevistado->setFotoEntrvAttribute($request->fotoEntrv, $request->codigEntrv);
		} else if ($request->img_default) {
			Storage::disk('local')->delete('/Imagenes/Entrevistados/' . substr($entrevistado->fotoEntrv, 33));
			$entrevistado->setFotoEntrvAttributeDefault();
		}
		$entrevistado->update([
			"codigEntrv" => $request->codigEntrv,
			"nombreApellidosEntrv" => $request->nombreApellidosEntrv,
			"sexoEntrv" => $request->sexoEntrv,
			"descripEspEntrv" => $request->descripEspEntrv,
		]);
		return response()->json($entrevistado);
	}

	public function destroyLog($id)  // DestroyLog | Método que Elimina de forma Lógica un Registro Específico del Modelo:Interprete
	{
		return response()->json(Entrevistado::findOrFail($id)->delete());
	}

	public function destroyFis($id)  // DestroyFis | Método que Elimina de forma Física un Registro Específico del Modelo:Interprete
	{
		$entrevistado = Entrevistado::withTrashed()->findOrFail($id);
		if (count($entrevistado->audiovisuales()->withTrashed()->get()) !== 0) {
			for ($i = count($entrevistado->audiovisuales()->withTrashed()->get()) - 1; $i >= 0; $i--) {
				$entrevistado->audiovisuales()->withTrashed()->get()[$i]->pivot->delete();
			}
		}
		if (substr($entrevistado->fotoEntrv, 33) !== "Logo ver vertical_Ltr Negras.png") {
			Storage::disk('local')->delete('/Imagenes/Entrevistados/' . substr($entrevistado->fotoEntrv, 33));
		}
		return response()->json($entrevistado->forceDelete());
	}

	public function restoreLog($id)  // RestoreLog | Método que Restaura un Registro Específico, eliminado de forma Lógica del Modelo:Interprete
	{
		return response()->json(Entrevistado::onlyTrashed()->findOrFail($id)->restore());
	}
}

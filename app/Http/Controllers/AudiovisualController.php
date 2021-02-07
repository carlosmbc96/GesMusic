<?php

namespace App\Http\Controllers;

use App\Audiovisual;
use App\Producto_Audiovisual;
use App\Vocabulario;
use App\Producto;
use App\Proyecto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Mockery\Undefined;

class AudiovisualController extends Controller
{
	public function index(Request $request)  // Index | Método que lista todos los Registros del Modelo:Audiovisual
	{
		$valorbuscado = $request->valorbuscado;
		$atributo = $request->atributo;
		if (($atributo) && ($valorbuscado)) {
			$audiovisuales = Audiovisual::withTrashed()->BusqSelect($atributo, $valorbuscado)->get();
		} else if ($valorbuscado) {
			$audiovisuales = Audiovisual::withTrashed()->BusqGeneral($valorbuscado)->get();
		} else {
			$audiovisuales = Audiovisual::withTrashed()->get();
		}
		$relaciones = $request->relations; //  Aqui se accede al objeto relations que viene por parámetros
		//  En este bloque If se verifica que exista un objeto relaciones, en caso de que exista se accede a la primera posición del arreglo, si es all lo que contiene entonces se devuelven todas las relaciones para el modelo en cuestión.
		if ($relaciones) {
			if ($relaciones[0] === 'all') {
				$i = 0;
				$length = count($audiovisuales);
				for ($i; $i < $length; $i++) {
					$audiovisuales[$i]->entrevistados;
					$audiovisuales[$i]->realizadores;
					$audiovisuales[$i]->elementos;
				}
			} else {
				$i = 0;;
				$lengthAudiovisuales = count($audiovisuales);
				$lengthRelaciones = count($relaciones);
				for ($i; $i < $lengthAudiovisuales; $i++) {
					for ($j = 0; $j < $lengthRelaciones; $j++) {
						$relacionesMetodo = (string)$relaciones[$j];
						$audiovisuales[$i]->$relacionesMetodo;
					}
				}
			}
		}
		return response()->json($audiovisuales);
	}

	public function nomenclators()  // Nomenclators | Método que carga los Nomencladores en el Modelo:Audiovisual
	{
		// Sección de Carga de Nomencladores a emplear en las vista
		$clasfAudiov = Vocabulario::findorFail(3)->terminos;  // Nomenclador: Clasificación Audiovisual
		$genAudiov = Vocabulario::findorFail(15)->terminos;  // Nomenclador: Géneros Audiovisuales
		$anos = Vocabulario::findorFail(2)->terminos;  // Nomenclador: Años
		$paises = Vocabulario::findorFail(23)->terminos;  // Nomenclador: Países
		$idiom = Vocabulario::findorFail(18)->terminos;  // Nomenclador: Idiomas
		$idiomSubt = Vocabulario::findorFail(19)->terminos;  // Nomenclador: Idiomas de Subtitulos
		//$derechosAud = Vocabulario::findorFail(11)->terminos;  // Nomenclador: Etiquetas
		$nacionalid = Vocabulario::findorFail(22)->terminos;  // Nomenclador: Nacionalidades
		//$estdContract= Vocabulario::findorFail(8)->terminos;  // Nomenclador: Estados Contractuales
		return response()->json([[$clasfAudiov], [$genAudiov], [$anos], [$paises], [$idiom], [$idiomSubt], [$nacionalid]]);  // Se envian las variables
	}

	public function store(Request $request)  // Store | Método que Guarda el Registro creado en el Modelo:Audiovisual
	{
		$audiovisual = Audiovisual::create([
			"codigAud" => $request->codigAud,
			"isrcAud" => $request->isrcAud,
			"tituloAud" => $request->tituloAud,
			"clasifAud" => $request->clasifAud,
			"generoAud" => $request->generoAud,
			"duracionAud" => $request->duracionAud,
			"añoFinAud" => $request->añoFinAud,
			"paisGrabAud" => $request->paisGrabAud,
			"idiomaAud" => $request->idiomaAud,
			"subtitulosAud" => $request->subtitulosAud,
			"etiquetasAud" => $request->etiquetasAud,
			"dueñoDerchAud" => $request->dueñoDerchAud,
			"derechosAud" => $request->derechosAud,
			"makingOfAud" => $request->makingOfAud,
			"descripEspAud" => $request->descripEspAud,
			"descripIngAud" => $request->descripIngAud,
			"dirArbolAud" => $request->dirArbolAud,
			"nacioDueñoDerchAud" => $request->nacioDueñoDerchAud,
			"fenomRefAud" => $request->fenomRefAud
		]);
		if ($request->product_id !== "undefined") {
			$productos = explode(",", $request->product_id);
			foreach ($productos as $producto) {
				Producto_Audiovisual::create([
					"producto_id" => $producto,
					"audiovisual_id" => $audiovisual->id
				]);
			}
			$this->crearDirectorios($productos, $request->codigAud);
		}
		if ($request->portadillaAud !== null) {
			$audiovisual->setPortadillaAudAttribute($request->portadillaAud, $request->codigAud);
		} else
			$audiovisual->setPortadillaAudAttributeDefault();
		$audiovisual->save();
		return response()->json($audiovisual);
	}

	public function update(Request $request)  // Update | Método que Actualiza un Registro Específico del Modelo:Audiovisual
	{
		$audiovisual = Audiovisual::findOrFail($request->id);
		if ($request->portadillaAud !== null) {
			if (substr($audiovisual->portadillaAud, 33) !== "Logo ver vertical_Ltr Negras.png") {
				Storage::disk('local')->delete(substr('/Imagenes/Audiovisuales/' . $audiovisual->portadillaAud, 33));
			}
			$audiovisual->setPortadillaAudAttribute($request->portadillaAud, $request->codigAud);
		} else if ($request->img_default) {
			Storage::disk('local')->delete('/Imagenes/Audiovisuales/' . substr($audiovisual->portadillaAud, 33));
			$audiovisual->setPortadillaAudAttributeDefault();
		}
		$audiovisual->update([
			"codigAud" => $request->codigAud,
			"isrcAud" => $request->isrcAud,
			"tituloAud" => $request->tituloAud,
			"clasifAud" => $request->clasifAud,
			"generoAud" => $request->generoAud,
			"duracionAud" => $request->duracionAud,
			"añoFinAud" => $request->añoFinAud,
			"paisGrabAud" => $request->paisGrabAud,
			"idiomaAud" => $request->idiomaAud,
			"subtitulosAud" => $request->subtitulosAud,
			"etiquetasAud" => $request->etiquetasAud,
			"dueñoDerchAud" => $request->dueñoDerchAud,
			"derechosAud" => $request->derechosAud,
			"nacioDueñoDerchAud" => $request->nacioDueñoDerchAud,
			"makingOfAud" => $request->makingOfAud,
			"descripEspAud" => $request->descripEspAud,
			"descripIngAud" => $request->descripIngAud,
			"dirArbolAud" => $request->dirArbolAud,
			"fenomRefAud" => $request->fenomRefAud
		]);
		$productosOld = [];
		$igual_cont = 0;
		if ($request->product_id !== null) {
			$productos = explode(",", $request->product_id);
			for ($i = 0; $i < count($audiovisual->productos); $i++) {
				array_push($productosOld, $audiovisual->productos[$i]->pivot->producto_id);
			}
			if (count($productos) === count($audiovisual->productos)) {
				for ($i = 0; $i < count($audiovisual->productos); $i++) {
					if ($audiovisual->productos[$i]->pivot->producto_id === $productos[$i]) {
						$igual_cont++;
					}
				}
				if ($igual_cont !== count($productos)) {
					$this->eliminarDirectorios($productosOld, $request->codigAud);
					$this->crearDirectorios($productos, $request->codigAud);
					$this->actualizarRelacion($audiovisual, $productos);
				}
			} else {
				$this->eliminarDirectorios($productosOld, $request->codigAud);
				$this->crearDirectorios($productos, $request->codigAud);
				$this->actualizarRelacion($audiovisual, $productos);
			}
		} else if (count($audiovisual->productos) !== 0) {
			for ($i = 0; $i < count($audiovisual->productos); $i++) {
				array_push($productosOld, $audiovisual->productos[$i]->pivot->producto_id);
				$audiovisual->productos[$i]->pivot->delete();
			}
			$this->eliminarDirectorios($productosOld, $request->codigAud);
		}
		return response()->json($audiovisual);
	}

	public function destroyLog($id)  // DestroyLog | Método que Elimina de forma Lógica un Registro Específico del Modelo:Audiovisual
	{
		return response()->json(Audiovisual::findOrFail($id)->delete());
	}

	public function destroyFis($id)  // DestroyFis | Método que Elimina de forma Física un Registro Específico del Modelo:Audiovisual
	{
		$productos = [];
		$audiovisual = Audiovisual::withTrashed()->findOrFail($id);
		if (count($audiovisual->productos()->withTrashed()->get()) !== 0) {
			for ($i = 0; $i < count($audiovisual->productos()->withTrashed()->get()); $i++) {
				array_push($productos, $audiovisual->productos()->withTrashed()->get()[$i]->pivot->producto_id);
				$audiovisual->productos()->withTrashed()->get()[$i]->pivot->delete();
			}
			$this->eliminarDirectorios($productos, $audiovisual->codigAud);
		}
		if (substr($audiovisual->portadillaAud, 33) !== "Logo ver vertical_Ltr Negras.png") {
			Storage::disk('local')->delete('/Imagenes/Audiovisuales/' . substr($audiovisual->portadillaAud, 33));
		}
		return response()->json($audiovisual->forceDelete());
	}

	public function restoreLog($id)  // RestoreLog | Método que Restaura un Registro Específico, eliminado de forma Lógica del Modelo:Audiovisual
	{
		return response()->json(Audiovisual::onlyTrashed()->findOrFail($id)->restore());
	}
	public function actualizarRelacion($audiovisual, $productos)  // RestoreLog | Método que Restaura un Registro Específico, eliminado de forma Lógica del Modelo:Audiovisual
	{
		for ($i = 0; $i < count($audiovisual->productos); $i++) {
			$audiovisual->productos[$i]->pivot->delete();
		}
		foreach ($productos as $producto) {
			Producto_Audiovisual::create([
				"producto_id" => $producto,
				"audiovisual_id" => $audiovisual->id
			]);
		}
	}
	public function crearDirectorios($productos, $codigAud)  // RestoreLog | Método que Restaura un Registro Específico, eliminado de forma Lógica del Modelo:Audiovisual
	{
		for ($i = 0; $i < count($productos); $i++) {
			$producto = Producto::withTrashed()->findOrFail($productos[$i]);
			$proyecto = Proyecto::withTrashed()->findOrFail($producto->proyecto_id);
			Storage::disk('local')->makeDirectory('/Proyectos/' . $proyecto->codigProy . "/" . $producto->codigProd . "/" . $codigAud);
		}
	}
	public function eliminarDirectorios($productos, $codigAud)
	{
		for ($i = 0; $i < count($productos); $i++) {
			$producto = Producto::findOrFail($productos[$i]);
			$proyecto = Proyecto::findOrFail($producto->proyecto_id);
			Storage::disk('local')->deleteDirectory('/Proyectos/' . $proyecto->codigProy . "/" . $producto->codigProd . "/" . $codigAud);
		}
	}
}

<?php

namespace App\Http\Controllers;

use App\Audiovisual;
use App\Audiovisual_Autor;
use App\Audiovisual_Entrevistado;
use App\Audiovisual_Interprete;
use App\Audiovisual_Realizador;
use App\Autor;
use App\Entrevistado;
use App\Interprete;
use App\Producto_Audiovisual;
use App\Vocabulario;
use App\Producto;
use App\Proyecto;
use App\Realizador;
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
					$audiovisuales[$i]->autores;
					$audiovisuales[$i]->interpretes;
					$audiovisuales[$i]->elementos;
				}
			} else {
				$i = 0;
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
		$rolesInterp = Vocabulario::findorFail(26)->terminos;  // Nomenclador: Roles de Intérpretes
		return response()->json([[$clasfAudiov], [$genAudiov], [$anos], [$paises], [$idiom], [$idiomSubt], [$nacionalid], [$rolesInterp]]);  // Se envian las variables
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
		if ($request->type_relation === "productos") {
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
		} else if ($request->type_relation === "autores") {
			$autores = explode(",", $request->autores_id);
			foreach ($autores as $autor) {
				Audiovisual_Autor::create([
					"autor_id" => $autor,
					"audiovisual_id" => $audiovisual->id
				]);
			}
		} else if ($request->type_relation === "interpretes") {
			$interpretes = explode(",", $request->interpretes_id);
			foreach ($interpretes as $interprete) {
				Audiovisual_Interprete::create([
					"interprete_id" => $interprete,
					"audiovisual_id" => $audiovisual->id
				]);
			}
		} else if ($request->type_relation === "realizadores") {
			$realizadores = explode(",", $request->realizadores_id);
			foreach ($realizadores as $realizador) {
				Audiovisual_Realizador::create([
					"realizador_id" => $realizador,
					"audiovisual_id" => $audiovisual->id
				]);
			}
		} else if ($request->type_relation === "entrevistados") {
			$entrevistados = explode(",", $request->entrevistados_id);
			foreach ($entrevistados as $entrevistado) {
				Audiovisual_Entrevistado::create([
					"entrevistado_id" => $entrevistado,
					"audiovisual_id" => $audiovisual->id
				]);
			}
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
			for ($i = count($audiovisual->productos()->withTrashed()->get()) - 1; $i >= 0; $i--) {
				array_push($productos, $audiovisual->productos()->withTrashed()->get()[$i]->pivot->producto_id);
				$audiovisual->productos()->withTrashed()->get()[$i]->pivot->delete();
			}
			$this->eliminarDirectorios($productos, $audiovisual->codigAud);
		}
		if (count($audiovisual->realizadores()->withTrashed()->get()) !== 0) {
			for ($i = count($audiovisual->realizadores()->withTrashed()->get()) - 1; $i >= 0; $i--) {
				$audiovisual->realizadores()->withTrashed()->get()[$i]->pivot->delete();
			}
		}
		if (count($audiovisual->entrevistados()->withTrashed()->get()) !== 0) {
			for ($i = count($audiovisual->entrevistados()->withTrashed()->get()) - 1; $i >= 0; $i--) {
				$audiovisual->entrevistados()->withTrashed()->get()[$i]->pivot->delete();
			}
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
	public function actualizarRelacionesAud(Request $request)
	{
		if ($request->relation === "productos") {
			$producto = Producto::withTrashed()->findOrFail($request->id);
			for ($i = count($producto->audiovisuales()->withTrashed()->get()) - 1; $i >= 0; $i--) {
				$producto->audiovisuales()->withTrashed()->get()[$i]->pivot->delete();
			}
			foreach ($request->audiovisuales as $audiovisual) {
				Producto_Audiovisual::create([
					"producto_id" => $request->id,
					"audiovisual_id" => $audiovisual
				]);
			}
			return response()->json($producto);
		} else if ($request->relation === "autores") {
			$autor = Autor::withTrashed()->findOrFail($request->id);
			for ($i = count($autor->audiovisuales()->withTrashed()->get()) - 1; $i >= 0; $i--) {
				$autor->audiovisuales()->withTrashed()->get()[$i]->pivot->delete();
			}
			foreach ($request->audiovisuales as $audiovisual) {
				Audiovisual_Autor::create([
					"autor_id" => $request->id,
					"audiovisual_id" => $audiovisual
				]);
			}
			return response()->json($autor);
		} else if ($request->relation === "interpretes") {
			$interprete = Interprete::withTrashed()->findOrFail($request->id);
			for ($i = count($interprete->audiovisuales()->withTrashed()->get()) - 1; $i >= 0; $i--) {
				$interprete->audiovisuales()->withTrashed()->get()[$i]->pivot->delete();
			}
			foreach ($request->audiovisuales as $audiovisual) {
				Audiovisual_Interprete::create([
					"interprete_id" => $request->id,
					"audiovisual_id" => $audiovisual
				]);
			}
			return response()->json($interprete);
		} else if ($request->relation === "realizadores") {
			$realizador = Realizador::withTrashed()->findOrFail($request->id);
			for ($i = count($realizador->audiovisuales()->withTrashed()->get()) - 1; $i >= 0; $i--) {
				$realizador->audiovisuales()->withTrashed()->get()[$i]->pivot->delete();
			}
			foreach ($request->audiovisuales as $audiovisual) {
				Audiovisual_Realizador::create([
					"realizador_id" => $request->id,
					"audiovisual_id" => $audiovisual
				]);
			}
			return response()->json($realizador);
		} else if ($request->relation === "entrevistados") {
			$entrevistado = Entrevistado::withTrashed()->findOrFail($request->id);
			for ($i = count($entrevistado->audiovisuales()->withTrashed()->get()) - 1; $i >= 0; $i--) {
				$entrevistado->audiovisuales()->withTrashed()->get()[$i]->pivot->delete();
			}
			foreach ($request->audiovisuales as $audiovisual) {
				Audiovisual_Entrevistado::create([
					"entrevistado_id" => $request->id,
					"audiovisual_id" => $audiovisual
				]);
			}
			return response()->json($entrevistado);
		}
	}

	public function realizadores(Request $request) {
		$audiovisual = Audiovisual::withTrashed()->findOrFail($request->id);
		for ($i = count($audiovisual->realizadores()->withTrashed()->get()) - 1; $i >= 0; $i--) {
			$audiovisual->realizadores()->withTrashed()->get()[$i]->pivot->delete();
		}
		foreach ($request->realizadores as $realizador) {
			Audiovisual_Realizador::create([
				"realizador_id" => $realizador,
				"audiovisual_id" => $request->id,
			]);
		}
		return response()->json($audiovisual);
	}

	public function entrevistados(Request $request) {
		$audiovisual = Audiovisual::withTrashed()->findOrFail($request->id);
		for ($i = count($audiovisual->entrevistados()->withTrashed()->get()) - 1; $i >= 0; $i--) {
			$audiovisual->entrevistados()->withTrashed()->get()[$i]->pivot->delete();
		}
		foreach ($request->entrevistados as $entrevistado) {
			Audiovisual_Entrevistado::create([
				"entrevistado_id" => $entrevistado,
				"audiovisual_id" => $request->id,
			]);
		}
		return response()->json($audiovisual);
	}

	public function autores(Request $request) {
		$audiovisual = Audiovisual::withTrashed()->findOrFail($request->id);
		for ($i = count($audiovisual->autores()->withTrashed()->get()) - 1; $i >= 0; $i--) {
			$audiovisual->autores()->withTrashed()->get()[$i]->pivot->delete();
		}
		foreach ($request->autores as $autor) {
			Audiovisual_Autor::create([
				"autor_id" => $autor,
				"audiovisual_id" => $request->id,
			]);
		}
		return response()->json($audiovisual);
	}

	public function interpretes(Request $request) {
		$audiovisual = Audiovisual::withTrashed()->findOrFail($request->id);
		for ($i = count($audiovisual->interpretes()->withTrashed()->get()) - 1; $i >= 0; $i--) {
			$audiovisual->interpretes()->withTrashed()->get()[$i]->pivot->delete();
		}
		for ($i = 0; $i < count($request->interpretes); $i++) {
			var_dump($request->interpretes[$i][1]);
			Audiovisual_Interprete::create([
					"interprete_id" => $request->interpretes[$i][0],
					"rolInterp" => $request->interpretes[$i][1],
					"audiovisual_id" => $request->id
			]);
	}
		return response()->json($audiovisual);
	}
}

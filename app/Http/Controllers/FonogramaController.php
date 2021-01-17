<?php

namespace App\Http\Controllers;

use App\Fonograma;
use App\Producto;
use App\Proyecto;
use App\Vocabulario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FonogramaController extends Controller
{
	public function index(Request $request)  // Index | Método que lista todos los Registros del Modelo:Fonograma
	{
		$valorbuscado = $request->valorbuscado;
		$atributo = $request->atributo;
		if (($atributo) && ($valorbuscado)) {
			$fonogramas = Fonograma::withTrashed()->BusqSelect($atributo, $valorbuscado)->get();
		} else if ($valorbuscado) {
			$fonogramas = Fonograma::withTrashed()->BusqGeneral($valorbuscado)->get();
		} else {
			$fonogramas = Fonograma::withTrashed()->get();
		}
		$relaciones = $request->relations; //  Aqui se accede al objeto relations que viene por parámetros
		//  En este bloque If se verifica que exista un objeto relaciones, en caso de que exista se accede a la primera posición del arreglo, si es all lo que contiene entonces se devuelven todas las relaciones para el modelo en cuestión.
		if ($relaciones) {
			if ($relaciones[0] === 'all') {
				$i = 0;
				$length = count($fonogramas);
				for ($i; $i < $length; $i++) {
					$fonogramas[$i]->elementos;
				}
			} else {
				$i = 0;;
				$lengthFonogramas = count($fonogramas);
				$lengthRelaciones = count($relaciones);
				for ($i; $i < $lengthFonogramas; $i++) {
					for ($j = 0; $j < $lengthRelaciones; $j++) {
						$relacionesMetodo = (string)$relaciones[$j];
						$fonogramas[$i]->$relacionesMetodo;
					}
				}
			}
		}
		return response()->json($fonogramas);
	}

	public function nomenclators()  // Nomenclators | Método que carga los Nomencladores en el Modelo:Fonograma
	{
		// Sección de Carga de Nomencladores a emplear en las vista
		$clasfFonog = Vocabulario::findorFail(4)->terminos;  // Nomenclador: Clasificación Fonograma
		$territorio = Vocabulario::findorFail(36)->terminos;  // Nomenclador: Territorios
		$nacionalid = Vocabulario::findorFail(22)->terminos;  // Nomenclador: Nacionalidades
		$estdContract = Vocabulario::findorFail(8)->terminos;  // Nomenclador: Estados Contractuales
		return response()->json([[$clasfFonog], [$territorio], [$nacionalid], [$estdContract]]);  // Se envian las variables
	}

	public function store(Request $request)  // Store | Método que Guarda el Registro creado en el Modelo:Fonograma
	{
		$fonograma = Fonograma::create([
			"codigFong" => $request->codigFong,
			"tituloFong" => $request->tituloFong,
			"clasficacionFong" => $request->clasficacionFong,
			"duracionFong" => $request->duracionFong,
			"territorioFong" => $request->territorioFong,
			"dueñoDerchFong" => $request->dueñoDerchFong,
			"nacioDueñoDerchFong" => $request->nacioDueñoDerchFong,
			"propiedadFong" => $request->propiedadFong,
			"dirArbolFong" => $request->dirArbolFong,
			"descripEspFong" => $request->descripEspFong,
			"descripIngFong" => $request->descripIngFong,
			// "producto_id" => $request->producto_id
		]);
		// $producto = Producto::findOrFail($request->producto_id);
		// $proyecto = Proyecto::findOrFail($producto->proyecto_id);
		// var_dump($proyecto->codigProy);
		// var_dump($producto->codigProd);
		// Storage::disk('local')->makeDirectory('/Proyectos/' . $proyecto->codigProy . "/" . $producto->codigProd . "/" . $request->codigFong);
		if ($request->portadillaFong !== null) {
			$fonograma->setPortadillaFongAttribute($request->portadillaFong, $request->codigFong);
		} else
			$fonograma->setPortadillaFongAttributeDefault();
		$fonograma->save();
		return response()->json($fonograma);
	}

	public function update(Request $request)  // Update | Método que Actualiza un Registro Específico del Modelo:Fonograma
	{
		$fonograma = Fonograma::findOrFail($request->id);
		if ($request->portadillaFong !== null) {
			if (substr($fonograma->portadillaFong, 30) !== "Logo ver vertical_Ltr Negras.png") {
				Storage::disk('local')->delete(substr('/Imagenes/Fonogramas/' . $fonograma->portadillaFong, 30));
			}
			$fonograma->setPortadillaFongAttribute($request->portadillaFong, $request->codigFong);
		} else if ($request->img_default) {
			Storage::disk('local')->delete('/Imagenes/Fonogramas/' . substr($fonograma->portadillaFong, 30));
			$fonograma->setPortadillaFongAttributeDefault();
		}
		$fonograma->update([
			"codigFong" => $request->codigFong,
			"tituloFong" => $request->tituloFong,
			"clasficacionFong" => $request->clasficacionFong,
			"duracionFong" => $request->duracionFong,
			"territorioFong" => $request->territorioFong,
			"dueñoDerchFong" => $request->dueñoDerchFong,
			"nacioDueñoDerchFong" => $request->nacioDueñoDerchFong,
			"propiedadFong" => $request->propiedadFong,
			"dirArbolFong" => $request->dirArbolFong,
			"descripEspFong" => $request->descripEspFong,
			"descripIngFong" => $request->descripIngFong,
			//"producto_id" => $request->producto_id
		]);
		return response()->json($fonograma);
	}

	public function destroyLog($id)  // DestroyLog | Método que Elimina de forma Lógica un Registro Específico del Modelo:Fonograma
	{
		return response()->json(Fonograma::findOrFail($id)->delete());
	}

	public function destroyFis($id)  // DestroyFis | Método que Elimina de forma Física un Registro Específico del Modelo:Fonograma
	{
		$fonograma = Fonograma::withTrashed()->findOrFail($id);
		//$producto = Producto::withTrashed()->findOrFail($fonograma->producto_id);
		//$proyecto = Proyecto::withTrashed()->findOrFail($producto->proyecto_id);
		//Storage::disk('local')->deleteDirectory('/Proyectos/' . $proyecto->codigProy . "/" . $producto->codigProd . "/" . $fonograma->codigFong);
		if (substr($fonograma->portadillaFong, 30) !== "Logo ver vertical_Ltr Negras.png") {
			Storage::disk('local')->delete('/Imagenes/Fonogramas/' . substr($fonograma->portadillaFong, 30));
		}
		return response()->json($fonograma->forceDelete());
	}

	public function restoreLog($id)  // RestoreLog | Método que Restaura un Registro Específico, eliminado de forma Lógica del Modelo:Fonograma
	{
		return response()->json(Fonograma::onlyTrashed()->findOrFail($id)->restore());
	}
}

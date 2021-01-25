<?php

namespace App\Http\Controllers;

use App\Fonograma;
use App\Producto;
use App\Producto_Fonograma;
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
                    $fonogramas[$i]->productos;
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
        $anos = Vocabulario::findorFail(2)->terminos;  // Nomenclador: Años
        $territorio = Vocabulario::findorFail(36)->terminos;  // Nomenclador: Territorios
        $nacionalid = Vocabulario::findorFail(22)->terminos;  // Nomenclador: Nacionalidades
        return response()->json([[$clasfFonog], [$territorio], [$nacionalid], [$anos]]);  // Se envian las variables
    }

    public function store(Request $request)  // Store | Método que Guarda el Registro creado en el Modelo:Fonograma
    {
        $fonograma = Fonograma::create([
            "codigFong" => $request->codigFong,
            "tituloFong" => $request->tituloFong,
            "clasficacionFong" => $request->clasficacionFong,
            "duracionFong" => $request->duracionFong,
            "añoFong" => $request->añoFong,
            "territorioFong" => $request->territorioFong,
            "dueñoDerchFong" => $request->dueñoDerchFong,
            "nacioDueñoDerchFong" => $request->nacioDueñoDerchFong,
            "propiedadFong" => $request->propiedadFong,
            "dirArbolFong" => $request->dirArbolFong,
            "descripEspFong" => $request->descripEspFong,
            "descripIngFong" => $request->descripIngFong,
        ]);
        if ($request->product_id !== "undefined") {
            $productos = explode(",", $request->product_id);
            foreach ($productos as $producto) {
                Producto_Fonograma::create([
                    "producto_id" => $producto,
                    "fonograma_id" => $fonograma->id
                ]);
            }
            $this->crearDirectorios($productos, $request->codigFong);
        }
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
            if (substr($fonograma->portadillaFong, 33) !== "Logo ver vertical_Ltr Negras.png") {
                Storage::disk('local')->delete(substr('/Imagenes/Fonogramas/' . $fonograma->portadillaFong, 33));
            }
            $fonograma->setPortadillaFongAttribute($request->portadillaFong, $request->codigFong);
        } else if ($request->img_default) {
            Storage::disk('local')->delete('/Imagenes/Fonogramas/' . substr($fonograma->portadillaFong, 33));
            $fonograma->setPortadillaFongAttributeDefault();
        }
        $fonograma->update([
            "codigFong" => $request->codigFong,
            "tituloFong" => $request->tituloFong,
            "clasficacionFong" => $request->clasficacionFong,
            "duracionFong" => $request->duracionFong,
            "territorioFong" => $request->territorioFong,
            "añoFong" => $request->añoFong,
            "dueñoDerchFong" => $request->dueñoDerchFong,
            "nacioDueñoDerchFong" => $request->nacioDueñoDerchFong,
            "propiedadFong" => $request->propiedadFong,
            "dirArbolFong" => $request->dirArbolFong,
            "descripEspFong" => $request->descripEspFong,
            "descripIngFong" => $request->descripIngFong,
        ]);
        $productosOld = [];
        $igual_cont = 0;
        if ($request->product_id !== null) {
            $productos = explode(",", $request->product_id);
            for ($i = 0; $i < count($fonograma->productos); $i++) {
                array_push($productosOld, $fonograma->productos[$i]->pivot->producto_id);
            }
            if (count($productos) === count($fonograma->productos)) {
                for ($i = 0; $i < count($fonograma->productos); $i++) {
                    if ($fonograma->productos[$i]->pivot->producto_id === $productos[$i]) {
                        $igual_cont++;
                    }
                }
                if ($igual_cont !== count($productos)) {
                    $this->eliminarDirectorios($productosOld, $request->codigFong);
                    $this->crearDirectorios($productos, $request->codigFong);
                    $this->actualizarRelacion($fonograma, $productos);
                }
            } else {
                $this->eliminarDirectorios($productosOld, $request->codigFong);
                $this->crearDirectorios($productos, $request->codigFong);
                $this->actualizarRelacion($fonograma, $productos);
            }
        } else if (count($fonograma->productos) !== 0) {
            for ($i = 0; $i < count($fonograma->productos); $i++) {
                array_push($productosOld, $fonograma->productos[$i]->pivot->producto_id);
                $fonograma->productos[$i]->pivot->delete();
            }
            $this->eliminarDirectorios($productosOld, $request->codigFong);
        }
        return response()->json($fonograma);
    }

    public function destroyLog($id)  // DestroyLog | Método que Elimina de forma Lógica un Registro Específico del Modelo:Fonograma
    {
        return response()->json(Fonograma::findOrFail($id)->delete());
    }

    public function destroyFis($id)  // DestroyFis | Método que Elimina de forma Física un Registro Específico del Modelo:Fonograma
    {
        $productos = [];
        $fonograma = Fonograma::withTrashed()->findOrFail($id);
        for ($i = 0; $i < count($fonograma->productos); $i++) {
            array_push($productos, $fonograma->productos[$i]->pivot->producto_id);
            $fonograma->productos[$i]->pivot->delete();
        }
        if (substr($fonograma->portadillaFong, 33) !== "Logo ver vertical_Ltr Negras.png") {
            Storage::disk('local')->delete('/Imagenes/Fonogramas/' . substr($fonograma->portadillaFong, 33));
        }
        return response()->json($fonograma->forceDelete());
    }

    public function restoreLog($id)  // RestoreLog | Método que Restaura un Registro Específico, eliminado de forma Lógica del Modelo:Fonograma
    {
        return response()->json(Fonograma::onlyTrashed()->findOrFail($id)->restore());
    }

    public function actualizarRelacion($fonograma, $productos)  // RestoreLog | Método que Restaura un Registro Específico, eliminado de forma Lógica del Modelo:Fonograma
    {
        for ($i = 0; $i < count($fonograma->productos); $i++) {
            $fonograma->productos[$i]->pivot->delete();
        }
        foreach ($productos as $producto) {
            Producto_Fonograma::create([
                "producto_id" => $producto,
                "fonograma_id" => $fonograma->id
            ]);
        }
    }
    public function crearDirectorios($productos, $codigFong)  // RestoreLog | Método que Restaura un Registro Específico, eliminado de forma Lógica del Modelo:Fonograma
    {
        for ($i = 0; $i < count($productos); $i++) {
            $producto = Producto::withTrashed()->findOrFail($productos[$i]);
            $proyecto = Proyecto::withTrashed()->findOrFail($producto->proyecto_id);
            Storage::disk('local')->makeDirectory('/Proyectos/' . $proyecto->codigProy . "/" . $producto->codigProd . "/" . $codigFong);
        }
    }
    public function eliminarDirectorios($productos, $codigFong)
    {
        for ($i = 0; $i < count($productos); $i++) {
            $producto = Producto::findOrFail($productos[$i]);
            $proyecto = Proyecto::findOrFail($producto->proyecto_id);
            Storage::disk('local')->deleteDirectory('/Proyectos/' . $proyecto->codigProy . "/" . $producto->codigProd . "/" . $codigFong);
        }
    }
}

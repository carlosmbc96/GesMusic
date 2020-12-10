<?php

namespace App\Http\Controllers;

use App\Proyecto;
use App\Vocabulario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProyectoController extends Controller
{
    public function index(Request $request)  // Index | Método que lista todos los Registros del Modelo:Proyecto
    {
        $valorbuscado = $request->valorbuscado;
        $atributo = $request->atributo;
        if (($atributo) && ($valorbuscado)) {
            $proyectos = Proyecto::withTrashed()->BusqSelect($atributo, $valorbuscado)->get();
        } else if ($valorbuscado) {
            $proyectos = Proyecto::withTrashed()->BusqGeneral($valorbuscado)->get();
        } else {
            $proyectos = Proyecto::withTrashed()->get();
        }
        $relaciones = $request->relations; //  Aqui se accede al objeto relations que viene por parámetros
        //  En este bloque If se verifica que exista un objeto relaciones, en caso de que exista se accede a la primera posición del arreglo, si es all lo que contiene entonces se devuelven todas las relaciones para el modelo en cuestión.
        if ($relaciones) {
            if ($relaciones[0] === 'all') {
                $i = 0;
                $length = count($proyectos);
                for ($i; $i < $length; $i++) {
                    $proyectos[$i]->empresa;
                    $proyectos[$i]->productos;
                }
            } else {
                $i = 0;;
                $lengthProyectos = count($proyectos);
                $lengthRelaciones = count($relaciones);
                for ($i; $i < $lengthProyectos; $i++) {
                    for ($j = 0; $j < $lengthRelaciones; $j++) {
                        $relacionesMetodo = (string)$relaciones[$j];
                        $proyectos[$i]->$relacionesMetodo;
                    }
                }
            }
        }
        return response()->json($proyectos);
    }

    public function nomenclators()  // Nomenclators | Método que carga los Nomencladores en el Modelo:Proyecto
    {
        // Sección de Carga de Nomencladores a emplear en las vista
        $anos = Vocabulario::findorFail(2)->terminos;  // Nomenclador: Años
        return response()->json($anos);  // Se envian las variables
    }

    public function store(Request $request)  // Store | Método que Guarda el Registro creado en el Modelo:Proyecto
    {
        $proyecto = Proyecto::create([
            "codigProy" => $request->codigProy,
            "añoProy" => $request->añoProy,
            "nombreProy" => $request->nombreProy,
            "descripEsp" => $request->descripEsp,
            "descripIng" => $request->descripIng,
            "empresa_id" => $request->empresa_id,
            "dirArbolProy" => $request->dirArbolProy,
        ]);
        Storage::disk('local')->makeDirectory($request->codigProy);
        if ($request->identificadorProy !== null) {
            $proyecto->setIdentificadorProyAttribute($request->identificadorProy, $request->codigProy);
        } else
            $proyecto->setIdentificadorProyAttributeDefault();
        $proyecto->save();
        return response()->json($proyecto);
    }

    public function update(Request $request)  // Update | Método que Actualiza un Registro Específico del Modelo:Proyecto
    {
        $proyecto = Proyecto::findOrFail($request->id);
        if ($request->identificadorProy !== null) {
            if (substr($proyecto->identificadorProy, 20) !== "Logo ver vertical_Ltr Negras.png") {
                Storage::disk('local')->delete(substr($proyecto->identificadorProy, 20));
            }
            $proyecto->setIdentificadorProyAttribute($request->identificadorProy, $request->codigProy);
        } else if ($request->img_default) {
            Storage::disk('local')->delete(substr($proyecto->identificadorProy, 20));
            $proyecto->setIdentificadorProyAttributeDefault();
        }
        $proyecto->update([
            "codigProy" => $request->codigProy,
            "añoProy" => $request->añoProy,
            "nombreProy" => $request->nombreProy,
            "descripEsp" => $request->descripEsp,
            "descripIng" => $request->descripIng,
            "empresa_id" => $request->empresa_id
        ]);
        return response()->json($proyecto);
    }

    public function destroyLog($id)  // DestroyLog | Método que Elimina de forma Lógica un Registro Específico del Modelo:Proyecto
    {
        $proyecto = Proyecto::findOrFail($id);
        $productos = $proyecto->productos;
        foreach ($productos as $producto) {
            $producto->delete();
        }
        $proyecto->delete();
        return response()->json($proyecto);
    }

    public function destroyFis($id)  // DestroyFis | Método que Elimina de forma Física un Registro Específico del Modelo:Proyecto
    {
        $proyecto = Proyecto::withTrashed()->findOrFail($id);
        Storage::disk('local')->deleteDirectory($proyecto->codigProy);
        if (substr($proyecto->identificadorProy, 20) !== "Logo ver vertical_Ltr Negras.png") {
            Storage::disk('local')->delete(substr($proyecto->identificadorProy, 20));
        }
        return response()->json($proyecto->forceDelete());
    }

    public function restoreLog($id)  // RestoreLog | Método que Restaura un Registro Específico, eliminado de forma Lógica del Modelo:Proyecto
    {
        $proyecto = Proyecto::onlyTrashed()->findOrFail($id);
        $productos = $proyecto->productos()->withTrashed()->get();
        foreach ($productos as $producto) {
            $producto->restore();
        }
        $proyecto->restore();
        return response()->json($proyecto);
    }
}

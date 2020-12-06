<?php

namespace App\Http\Controllers;

use App\Proyecto;
use App\Vocabulario;
use Illuminate\Http\Request;

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
        $proyecto = Proyecto::create($request->all());
        if ($request->identificadorProy !== null) {
            $proyecto->setIdentificadorProyAttribute($request->identificadorProy);
        } else
            $proyecto->setIdentificadorProyAttributeDefault();
        $proyecto->save();
        return response()->json($proyecto);
    }

    public function update(Request $request)  // Update | Método que Actualiza un Registro Específico del Modelo:Proyecto
    {
        $proyecto = Proyecto::findOrFail($request->id);
        if ($request->identificadorProy !== null) {
            $proyecto->setIdentificadorProyAttribute($request->identificadorProy);
        } else if ($request->img_default) {
            $proyecto->setIdentificadorProyAttributeDefault();
        }
        return response()->json($proyecto->update($request->all()));
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
        return response()->json(Proyecto::withTrashed()->findOrFail($id)->forceDelete());
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

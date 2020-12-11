<?php

namespace App\Http\Controllers;

use App\Elemento;
use App\Vocabulario;
use Illuminate\Http\Request;

class ElementoController extends Controller
{
    public function index(Request $request)  // Index | Método que lista todos los Registros del Modelo:Elemento
    {
        $valorbuscado = $request->valorbuscado;
        $atributo = $request->atributo;
        if ( ($atributo) && ($valorbuscado) ) {
            $elementos=Elemento::BusqSelect($atributo, $valorbuscado)->paginate(5);
        } else if($valorbuscado){
            $elementos=Elemento::BusqGeneral($valorbuscado)->paginate(5);
        } else {
            $elementos=Elemento::withTrashed()->get();
        }
        $relaciones = $request->relations; //  Aqui se accede al objeto relations que viene por parámetros
        //  En este bloque If se verifica que exista un objeto relaciones, en caso de que exista se accede a la primera posición del arreglo, si es all lo que contiene entonces se devuelven todas las relaciones para el modelo en cuestión.
        if ($relaciones) {
            if ($relaciones[0] === 'all') {
                $i=0;
                $length = count($elementos);
                for ($i ; $i < $length; $i++) {
                    $elementos[$i]->productos;
                    $elementos[$i]->audiovisuales;
                    $elementos[$i]->fonogramas;
                }
            }
            else {
                $i=0;;
                $lengthElementos = count($elementos);
                $lengthRelaciones = count($relaciones);
                for ($i; $i < $lengthElementos; $i++) {
                    for ($j=0; $j < $lengthRelaciones; $j++) {
                        $relacionesMetodo = (string)$relaciones[$j];
                        $elementos[$i]->$relacionesMetodo;
                    }
                }
            }
        }
        return response()->json($elementos);
    }

    public function nomenclators()  // Nomenclators | Método que carga los Nomencladores en el Modelo:Elemento
    {
        // Sección de Carga de Nomencladores a emplear en las vista
        $tipsElemen= Vocabulario::findorFail(34)->terminos;  // Nomenclador: Tipos de Elementos
        return response()->json($tipsElemen);  // Se envian las variables
    }

    public function store(Request $request)  // Store | Método que Guarda el Registro creado en el Modelo:Elemento
    {
        return response()->json(Elemento::create($request->all()));
    }

    public function update(Request $request,Elemento $elemento, $id)  // Update | Método que Actualiza un Registro Específico del Modelo:Elemento
    {
        return response()->json(Elemento::findOrFail($id)->update($request->all()));
    }

    public function destroyLog(Elemento $elemento, $id)  // DestroyLog | Método que Elimina de forma Lógica un Registro Específico del Modelo:Elemento
    {
        return response()->json(Elemento::findOrFail($id)->delete());
    }

    public function destroyFis(Elemento $elemento, $id)  // DestroyFis | Método que Elimina de forma Física un Registro Específico del Modelo:Elemento
    {
        return response()->json(Elemento::findOrFail($id)->forceDelete());
    }

    public function restoreLog(Elemento $elemento, $id)  // RestoreLog | Método que Restaura un Registro Específico, eliminado de forma Lógica del Modelo:Elemento
    {
        return response()->json(Elemento::findOrFail($id)->restore());
    }
}

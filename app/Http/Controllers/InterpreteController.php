<?php

namespace App\Http\Controllers;

use App\Interprete;
use Illuminate\Http\Request;

class InterpreteController extends Controller
{
    public function index(Request $request)  // Index | Método que lista todos los Registros del Modelo:Interprete
    {
        $valorbuscado = $request->valorbuscado;
        $atributo = $request->atributo;
        if ( ($atributo) && ($valorbuscado) ) {
            $interpretes=Interprete::BusqSelect($atributo, $valorbuscado)->paginate(5);
        } else if($valorbuscado){
            $interpretes=Interprete::BusqGeneral($valorbuscado)->paginate(5);
        } else {
            $interpretes=Interprete::withTrashed()->get();
        }
        $relaciones = $request->relations; //  Aqui se accede al objeto relations que viene por parámetros
        //  En este bloque If se verifica que exista un objeto relaciones, en caso de que exista se accede a la primera posición del arreglo, si es all lo que contiene entonces se devuelven todas las relaciones para el modelo en cuestión.
        if ($relaciones) {
            if ($relaciones[0] === 'all') {
                $i=0;
                $length = count($interpretes);
                for ($i ; $i < $length; $i++) {
                    $interpretes[$i]->artisticos;
                }
            }
            else {
                $i=0;;
                $lengthInterpretes = count($interpretes);
                $lengthRelaciones = count($relaciones);
                for ($i; $i < $lengthInterpretes; $i++) {
                    for ($j=0; $j < $lengthRelaciones; $j++) {
                        $relacionesMetodo = (string)$relaciones[$j];
                        $interpretes[$i]->$relacionesMetodo;
                    }
                }
            }
        }
        return response()->json($interpretes);
    }

    public function store(Request $request)  // Store | Método que Guarda el Registro creado en el Modelo:Interprete
    {
        return response()->json(Interprete::create($request->all()));
    }

    public function update(Request $request,Interprete $interprete, $id)  // Update | Método que Actualiza un Registro Específico del Modelo:Interprete
    {
        return response()->json(Interprete::findOrFail($id)->update($request->all()));
    }

    public function destroyLog(Interprete $interprete, $id)  // DestroyLog | Método que Elimina de forma Lógica un Registro Específico del Modelo:Interprete
    {
        return response()->json(Interprete::findOrFail($id)->delete());
    }

    public function destroyFis(Interprete $interprete, $id)  // DestroyFis | Método que Elimina de forma Física un Registro Específico del Modelo:Interprete
    {
        return response()->json(Interprete::findOrFail($id)->forceDelete());
    }

    public function restoreLog(Interprete $interprete, $id)  // RestoreLog | Método que Restaura un Registro Específico, eliminado de forma Lógica del Modelo:Interprete
    {
        return response()->json(Interprete::findOrFail($id)->restore());
    }
}

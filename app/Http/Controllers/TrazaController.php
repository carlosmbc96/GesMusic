<?php

namespace App\Http\Controllers;

use App\Traza;
use Illuminate\Http\Request;

class TrazaController extends Controller
{
    public function index(Request $request)  // Index | Método que lista todos los Registros del Modelo:Traza
    {
        $valorbuscado = $request->valorbuscado;
        $atributo = $request->atributo;
        if ( ($atributo) && ($valorbuscado) ) {
            $trazas=Traza::BusqSelect($atributo, $valorbuscado)->paginate(5);
        } else if($valorbuscado){
            $trazas=Traza::BusqGeneral($valorbuscado)->paginate(5);
        } else {
            $trazas=Traza::withTrashed()->get();
        }
        $relaciones = $request->relations; //  Aqui se accede al objeto relations que viene por parámetros
        //  En este bloque If se verifica que exista un objeto relaciones, en caso de que exista se accede a la primera posición del arreglo, si es all lo que contiene entonces se devuelven todas las relaciones para el modelo en cuestión.
        if ($relaciones) {
            if ($relaciones[0] === 'all') {
                $i=0;
                $length = count($trazas);
                for ($i ; $i < $length; $i++) {
                    $trazas[$i]->usuarios;
                }
            }
            else {
                $i=0;;
                $lengthTrazas = count($trazas);
                $lengthRelaciones = count($relaciones);
                for ($i; $i < $lengthTrazas; $i++) {
                    for ($j=0; $j < $lengthRelaciones; $j++) {
                        $relacionesMetodo = (string)$relaciones[$j];
                        $trazas[$i]->$relacionesMetodo;
                    }
                }
            }
        }
        return response()->json($trazas);
    }

    public function store(Request $request)  // Store | Método que Guarda el Registro creado en el Modelo:Traza
    {
        return response()->json(Traza::create($request->all()));
    }

    public function update(Request $request,Traza $traza, $id)  // Update | Método que Actualiza un Registro Específico del Modelo:Traza
    {
        return response()->json(Traza::findOrFail($id)->update($request->all()));
    }

    public function destroyLog(Traza $traza, $id)  // DestroyLog | Método que Elimina de forma Lógica un Registro Específico del Modelo:Traza
    {
        return response()->json(Traza::findOrFail($id)->delete());
    }

    public function destroyFis(Traza $traza, $id)  // DestroyFis | Método que Elimina de forma Física un Registro Específico del Modelo:Traza
    {
        return response()->json(Traza::findOrFail($id)->forceDelete());
    }

    public function restoreLog(Traza $traza, $id)  // RestoreLog | Método que Restaura un Registro Específico, eliminado de forma Lógica del Modelo:Traza
    {
        return response()->json(Traza::findOrFail($id)->restore());
    }
}

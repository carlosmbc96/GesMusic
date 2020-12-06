<?php

namespace App\Http\Controllers;

use App\Termino;
use Illuminate\Http\Request;

class TerminoController extends Controller
{
    public function index(Request $request)  // Index | Método que lista todos los Registros del Modelo:Termino
    {
        $valorbuscado = $request->valorbuscado;
        $atributo = $request->atributo;
        if ( ($atributo) && ($valorbuscado) ) {
            $terminos=Termino::BusqSelect($atributo, $valorbuscado)->paginate(5);
        } else if($valorbuscado){
            $terminos=Termino::BusqGeneral($valorbuscado)->paginate(5);
        } else {
            $terminos=Termino::All()->paginate(5);
        }
        $relaciones = $request->relations; //  Aqui se accede al objeto relations que viene por parámetros
        //  En este bloque If se verifica que exista un objeto relaciones, en caso de que exista se accede a la primera posición del arreglo, si es all lo que contiene entonces se devuelven todas las relaciones para el modelo en cuestión.
        if ($relaciones) {
            if ($relaciones[0] === 'all') {
                $i=0;
                $length = count($terminos);
                for ($i ; $i < $length; $i++) {
                    $terminos[$i]->vocabularios;
                }
            }
            else {
                $i=0;;
                $lengthTerminos = count($terminos);
                $lengthRelaciones = count($relaciones);
                for ($i; $i < $lengthTerminos; $i++) {
                    for ($j=0; $j < $lengthRelaciones; $j++) {
                        $relacionesMetodo = (string)$relaciones[$j];
                        $terminos[$i]->$relacionesMetodo;
                    }
                }
            }
        }
        return response()->json($terminos);
    }

    public function store(Request $request)  // Store | Método que Guarda el Registro creado en el Modelo:Termino
    {
        return response()->json(Termino::create($request->all()));
    }

    public function update(Request $request,Termino $termino, $id)  // Update | Método que Actualiza un Registro Específico del Modelo:Termino
    {
        return response()->json(Termino::findOrFail($id)->update($request->all()));
    }

    public function destroyLog(Termino $termino, $id)  // DestroyLog | Método que Elimina de forma Lógica un Registro Específico del Modelo:Termino
    {
        return response()->json(Termino::findOrFail($id)->delete());
    }

    public function destroyFis(Termino $termino, $id)  // DestroyFis | Método que Elimina de forma Física un Registro Específico del Modelo:Termino
    {
        return response()->json(Termino::findOrFail($id)->forceDelete());
    }

    public function restoreLog(Termino $termino, $id)  // RestoreLog | Método que Restaura un Registro Específico, eliminado de forma Lógica del Modelo:Termino
    {
        return response()->json(Termino::findOrFail($id)->restore());
    }
}

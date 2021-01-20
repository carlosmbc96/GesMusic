<?php

namespace App\Http\Controllers;

use App\Artistico;
use Illuminate\Http\Request;

class ArtisticoController extends Controller
{
    public function index(Request $request)  // Index | Método que lista todos los Registros del Modelo:Artistico
    {
        $valorbuscado = $request->valorbuscado;
        $atributo = $request->atributo;
        if ( ($atributo) && ($valorbuscado) ) {
            $artisticos=Artistico::withTrashed()->BusqSelect($atributo, $valorbuscado)->get();
        } else if($valorbuscado){
            $artisticos=Artistico::withTrashed()->BusqGeneral($valorbuscado)->get();
        } else {
            $artisticos=Artistico::withTrashed()->get();
        }
        $relaciones = $request->relations; //  Aqui se accede al objeto relations que viene por parámetros
        //  En este bloque If se verifica que exista un objeto relaciones, en caso de que exista se accede a la primera posición del arreglo, si es all lo que contiene entonces se devuelven todas las relaciones para el modelo en cuestión.
        if ($relaciones) {
            if ($relaciones[0] === 'all') {
                $i=0;
                $length = count($artisticos);
                for ($i ; $i < $length; $i++) {
                    $artisticos[$i]->interprete;
                }
            }
            else {
                $i=0;;
                $lengthArtisticos = count($artisticos);
                $lengthRelaciones = count($relaciones);
                for ($i; $i < $lengthArtisticos; $i++) {
                    for ($j=0; $j < $lengthRelaciones; $j++) {
                        $relacionesMetodo = (string)$relaciones[$j];
                        $artisticos[$i]->$relacionesMetodo;
                    }
                }
            }
        }
        return response()->json($artisticos);
    }

    public function store(Request $request)  // Store | Método que Guarda el Registro creado en el Modelo:Artistico
    {
        return response()->json(Artistico::create($request->all()));
    }

    public function update(Request $request)  // Update | Método que Actualiza un Registro Específico del Modelo:Artistico
    {
        return response()->json(Artistico::findOrFail($request->id)->update($request->all()));
    }

    public function destroyLog($id)  // DestroyLog | Método que Elimina de forma Lógica un Registro Específico del Modelo:Artistico
    {
        return response()->json(Artistico::findOrFail($id)->delete());
    }

    public function destroyFis($id)  // DestroyFis | Método que Elimina de forma Física un Registro Específico del Modelo:Artistico
    {
        return response()->json(Artistico::withTrashed()->findOrFail($id)->forceDelete());
    }

    public function restoreLog($id)  // RestoreLog | Método que Restaura un Registro Específico, eliminado de forma Lógica del Modelo:Artistico
    {
        return response()->json(Artistico::onlyTrashed()->findOrFail($id)->restore());
    }
}

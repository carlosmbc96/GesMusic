<?php

namespace App\Http\Controllers;

use App\Artistico;
use http\Env\Response;
use Illuminate\Http\Request;

class ArtisticoController extends Controller
{
    public function index(Request $request)  // Index | Método que lista todos los Registros del Modelo:Artistico
    {
        $valorbuscado = $request->valorbuscado;
        $atributo = $request->atributo;
        if ( ($atributo) && ($valorbuscado) ) {
            $artisticos=Artistico::BusqSelect($atributo, $valorbuscado)->paginate(5);
        } else if($valorbuscado){
            $artisticos=Artistico::BusqGeneral($valorbuscado)->paginate(5);
        } else {
            $artisticos=Artistico::All()->paginate(5);
        }
        $relaciones = $request->relations; //  Aqui se accede al objeto relations que viene por parámetros
        //  En este bloque If se verifica que exista un objeto relaciones, en caso de que exista se accede a la primera posición del arreglo, si es all lo que contiene entonces se devuelven todas las relaciones para el modelo en cuestión.
        if ($relaciones) {
            if ($relaciones[0] === 'all') {
                $i=0;
                $length = count($artisticos);
                for ($i ; $i < $length; $i++) {
                    $artisticos[$i]->interpretes;
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

    public function update(Request $request,Artistico $artistico, $id)  // Update | Método que Actualiza un Registro Específico del Modelo:Artistico
    {
        return response()->json(Artistico::findOrFail($id)->update($request->all()));
    }

    public function destroyLog(Artistico $artistico, $id)  // DestroyLog | Método que Elimina de forma Lógica un Registro Específico del Modelo:Artistico
    {
        return response()->json(Artistico::findOrFail($id)->delete());
    }

    public function destroyFis(Artistico $artistico, $id)  // DestroyFis | Método que Elimina de forma Física un Registro Específico del Modelo:Artistico
    {
        return response()->json(Artistico::findOrFail($id)->forceDelete());
    }

    public function restoreLog(Artistico $artistico, $id)  // RestoreLog | Método que Restaura un Registro Específico, eliminado de forma Lógica del Modelo:Artistico
    {
        return response()->json(Artistico::findOrFail($id)->restore());
    }
}

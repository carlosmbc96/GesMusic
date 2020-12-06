<?php

namespace App\Http\Controllers;

use App\Vocabulario;
use Illuminate\Http\Request;

class VocabularioController extends Controller
{
    public function index(Request $request)  // Index | Método que lista todos los Registros del Modelo:Vocabulario
    {
        $valorbuscado = $request->valorbuscado;
        $atributo = $request->atributo;
        if ( ($atributo) && ($valorbuscado) ) {
            $vocabularios=Vocabulario::BusqSelect($atributo, $valorbuscado)->paginate(5);
        } else if($valorbuscado){
            $vocabularios=Vocabulario::BusqGeneral($valorbuscado)->paginate(5);
        } else {
            $vocabularios=Vocabulario::All()->paginate(5);
        }
        $relaciones = $request->relations; //  Aqui se accede al objeto relations que viene por parámetros
        //  En este bloque If se verifica que exista un objeto relaciones, en caso de que exista se accede a la primera posición del arreglo, si es all lo que contiene entonces se devuelven todas las relaciones para el modelo en cuestión.
        if ($relaciones) {
            if ($relaciones[0] === 'all') {
                $i=0;
                $length = count($vocabularios);
                for ($i ; $i < $length; $i++) {
                    $vocabularios[$i]->terminos;
                }
            }
            else {
                $i=0;;
                $lengthVocabularios = count($vocabularios);
                $lengthRelaciones = count($relaciones);
                for ($i; $i < $lengthVocabularios; $i++) {
                    for ($j=0; $j < $lengthRelaciones; $j++) {
                        $relacionesMetodo = (string)$relaciones[$j];
                        $vocabularios[$i]->$relacionesMetodo;
                    }
                }
            }
        }
        return response()->json($vocabularios);
    }

    public function store(Request $request)  // Store | Método que Guarda el Registro creado en el Modelo:Vocabulario
    {
        return response()->json(Vocabulario::create($request->all()));
    }

    public function update(Request $request,Vocabulario $vocabulario, $id)  // Update | Método que Actualiza un Registro Específico del Modelo:Vocabulario
    {
        return response()->json(Vocabulario::findOrFail($id)->update($request->all()));
    }

    public function destroyLog(Vocabulario $vocabulario, $id)  // DestroyLog | Método que Elimina de forma Lógica un Registro Específico del Modelo:Vocabulario
    {
        return response()->json(Vocabulario::findOrFail($id)->delete());
    }

    public function destroyFis(Vocabulario $vocabulario, $id)  // DestroyFis | Método que Elimina de forma Física un Registro Específico del Modelo:Vocabulario
    {
        return response()->json(Vocabulario::findOrFail($id)->forceDelete());
    }

    public function restoreLog(Vocabulario $vocabulario, $id)  // RestoreLog | Método que Restaura un Registro Específico, eliminado de forma Lógica del Modelo:Vocabulario
    {
        return response()->json(Vocabulario::findOrFail($id)->restore());
    }
}

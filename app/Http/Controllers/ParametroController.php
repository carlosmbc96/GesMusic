<?php

namespace App\Http\Controllers;

use App\Parametro;
use Illuminate\Http\Request;

class ParametroController extends Controller
{
    public function index(Request $request)  // Index | Método que lista todos los Registros del Modelo:Parametro
    {
        $valorbuscado = $request->valorbuscado;
        $atributo = $request->atributo;
        if ( ($atributo) && ($valorbuscado) ) {
            $parametros=Parametro::BusqSelect($atributo, $valorbuscado)->paginate(5);
        } else if($valorbuscado){
            $parametros=Parametro::BusqGeneral($valorbuscado)->paginate(5);
        } else {
            $parametros=Parametro::withTrashed()->get();
        }
        $relaciones = $request->relations; //  Aqui se accede al objeto relations que viene por parámetros
        //  En este bloque If se verifica que exista un objeto relaciones, en caso de que exista se accede a la primera posición del arreglo, si es all lo que contiene entonces se devuelven todas las relaciones para el modelo en cuestión.
        if ($relaciones) {
            if ($relaciones[0] === 'all') {
                $i=0;
                $length = count($parametros);
                for ($i ; $i < $length; $i++) {
                }
            }
            else {
                $i=0;;
                $lengthParametros = count($parametros);
                $lengthRelaciones = count($relaciones);
                for ($i; $i < $lengthParametros; $i++) {
                    for ($j=0; $j < $lengthRelaciones; $j++) {
                        $relacionesMetodo = (string)$relaciones[$j];
                        $parametros[$i]->$relacionesMetodo;
                    }
                }
            }
        }
        return response()->json($parametros);
    }

    public function store(Request $request)  // Store | Método que Guarda el Registro creado en el Modelo:Parametro
    {
        return response()->json(Parametro::create($request->all()));
    }

    public function update(Request $request,Parametro $parametro, $id)  // Update | Método que Actualiza un Registro Específico del Modelo:Parametro
    {
        return response()->json(Parametro::findOrFail($id)->update($request->all()));
    }

    public function destroyLog(Parametro $parametro, $id)  // DestroyLog | Método que Elimina de forma Lógica un Registro Específico del Modelo:Parametro
    {
        return response()->json(Parametro::findOrFail($id)->delete());
    }

    public function destroyFis(Parametro $parametro, $id)  // DestroyFis | Método que Elimina de forma Física un Registro Específico del Modelo:Parametro
    {
        return response()->json(Parametro::findOrFail($id)->forceDelete());
    }

    public function restoreLog(Parametro $parametro, $id)  // RestoreLog | Método que Restaura un Registro Específico, eliminado de forma Lógica del Modelo:Parametro
    {
        return response()->json(Parametro::findOrFail($id)->restore());
    }
}

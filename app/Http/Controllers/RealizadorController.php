<?php

namespace App\Http\Controllers;

use App\Realizador;
use App\Vocabulario;
use Illuminate\Http\Request;

class RealizadorController extends Controller
{
    public function index(Request $request)  // Index | Método que lista todos los Registros del Modelo:Realizador
    {
        $valorbuscado = $request->valorbuscado;
        $atributo = $request->atributo;
        if ( ($atributo) && ($valorbuscado) ) {
            $realizadores=Realizador::BusqSelect($atributo, $valorbuscado)->paginate(5);
        } else if($valorbuscado){
            $realizadores=Realizador::BusqGeneral($valorbuscado)->paginate(5);
        } else {
            $realizadores=Realizador::withTrashed()->get();
        }
        $relaciones = $request->relations; //  Aqui se accede al objeto relations que viene por parámetros
        //  En este bloque If se verifica que exista un objeto relaciones, en caso de que exista se accede a la primera posición del arreglo, si es all lo que contiene entonces se devuelven todas las relaciones para el modelo en cuestión.
        if ($relaciones) {
            if ($relaciones[0] === 'all') {
                $i=0;
                $length = count($realizadores);
                for ($i ; $i < $length; $i++) {
                    $realizadores[$i]->audiovisuales;
                }
            }
            else {
                $i=0;;
                $lengthRealizadores = count($realizadores);
                $lengthRelaciones = count($relaciones);
                for ($i; $i < $lengthRealizadores; $i++) {
                    for ($j=0; $j < $lengthRelaciones; $j++) {
                        $relacionesMetodo = (string)$relaciones[$j];
                        $realizadores[$i]->$relacionesMetodo;
                    }
                }
            }
        }
        return response()->json($realizadores);
    }

    public function nomenclators()  // Nomenclators | Método que carga los Nomencladores en el Modelo:Realizador
    {
        // Sección de Carga de Nomencladores a emplear en las vista
        $sexos= Vocabulario::findorFail(28)->terminos;  // Nomenclador: Sexos
        return response()->json($sexos);  // Se envian las variables
    }

    public function store(Request $request)  // Store | Método que Guarda el Registro creado en el Modelo:Realizador
    {
        return response()->json(Realizador::create($request->all()));
    }

    public function update(Request $request,Realizador $realizador, $id)  // Update | Método que Actualiza un Registro Específico del Modelo:Realizador
    {
        return response()->json(Realizador::findOrFail($id)->update($request->all()));
    }

    public function destroyLog(Realizador $realizador, $id)  // DestroyLog | Método que Elimina de forma Lógica un Registro Específico del Modelo:Realizador
    {
        return response()->json(Realizador::findOrFail($id)->delete());
    }

    public function destroyFis(Realizador $realizador, $id)  // DestroyFis | Método que Elimina de forma Física un Registro Específico del Modelo:Realizador
    {
        return response()->json(Realizador::findOrFail($id)->forceDelete());
    }

    public function restoreLog(Realizador $realizador, $id)  // RestoreLog | Método que Restaura un Registro Específico, eliminado de forma Lógica del Modelo:Realizador
    {
        return response()->json(Realizador::findOrFail($id)->restore());
    }
}

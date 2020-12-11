<?php

namespace App\Http\Controllers;

use App\Fonograma;
use App\Vocabulario;
use Illuminate\Http\Request;

class FonogramaController extends Controller
{
    public function index(Request $request)  // Index | Método que lista todos los Registros del Modelo:Fonograma
    {
        $valorbuscado = $request->valorbuscado;
        $atributo = $request->atributo;
        if ( ($atributo) && ($valorbuscado) ) {
            $fonogramas=Fonograma::BusqSelect($atributo, $valorbuscado)->paginate(5);
        } else if($valorbuscado){
            $fonogramas=Fonograma::BusqGeneral($valorbuscado)->paginate(5);
        } else {
            $fonogramas=Fonograma::withTrashed()->get();
        }
        $relaciones = $request->relations; //  Aqui se accede al objeto relations que viene por parámetros
        //  En este bloque If se verifica que exista un objeto relaciones, en caso de que exista se accede a la primera posición del arreglo, si es all lo que contiene entonces se devuelven todas las relaciones para el modelo en cuestión.
        if ($relaciones) {
            if ($relaciones[0] === 'all') {
                $i=0;
                $length = count($fonogramas);
                for ($i ; $i < $length; $i++) {
                    $fonogramas[$i]->elementos;
                }
            }
            else {
                $i=0;;
                $lengthFonogramas = count($fonogramas);
                $lengthRelaciones = count($relaciones);
                for ($i; $i < $lengthFonogramas; $i++) {
                    for ($j=0; $j < $lengthRelaciones; $j++) {
                        $relacionesMetodo = (string)$relaciones[$j];
                        $fonogramas[$i]->$relacionesMetodo;
                    }
                }
            }
        }
        return response()->json($fonogramas);
    }

    public function nomenclators()  // Nomenclators | Método que carga los Nomencladores en el Modelo:Fonograma
    {
        // Sección de Carga de Nomencladores a emplear en las vista
        $clasfFonog= Vocabulario::findorFail(4)->terminos;  // Nomenclador: Clasificación Fonograma
        $territorio= Vocabulario::findorFail(36)->terminos;  // Nomenclador: Territorios
        $nacionalid= Vocabulario::findorFail(22)->terminos;  // Nomenclador: Nacionalidades
        $estdContract= Vocabulario::findorFail(8)->terminos;  // Nomenclador: Estados Contractuales
        return response()->json($clasfFonog,$territorio,$nacionalid,$estdContract);  // Se envian las variables
    }

    public function store(Request $request)  // Store | Método que Guarda el Registro creado en el Modelo:Fonograma
    {
        return response()->json(Fonograma::create($request->all()));
    }

    public function update(Request $request,Fonograma $fonograma, $id)  // Update | Método que Actualiza un Registro Específico del Modelo:Fonograma
    {
        return response()->json(Fonograma::findOrFail($id)->update($request->all()));
    }

    public function destroyLog(Fonograma $fonograma, $id)  // DestroyLog | Método que Elimina de forma Lógica un Registro Específico del Modelo:Fonograma
    {
        return response()->json(Fonograma::findOrFail($id)->delete());
    }

    public function destroyFis(Fonograma $fonograma, $id)  // DestroyFis | Método que Elimina de forma Física un Registro Específico del Modelo:Fonograma
    {
        return response()->json(Fonograma::findOrFail($id)->forceDelete());
    }

    public function restoreLog(Fonograma $fonograma, $id)  // RestoreLog | Método que Restaura un Registro Específico, eliminado de forma Lógica del Modelo:Fonograma
    {
        return response()->json(Fonograma::findOrFail($id)->restore());
    }
}

<?php

namespace App\Http\Controllers;

use App\Audiovisual;
use Illuminate\Http\Request;

class AudiovisualController extends Controller
{
    public function index(Request $request)  // Index | Método que lista todos los Registros del Modelo:Audiovisual
    {
        $valorbuscado = $request->valorbuscado;
        $atributo = $request->atributo;
        if ( ($atributo) && ($valorbuscado) ) {
            $audiovisuales=Audiovisual::BusqSelect($atributo, $valorbuscado)->paginate(5);
        } else if($valorbuscado){
            $audiovisuales=Audiovisual::BusqGeneral($valorbuscado)->paginate(5);
        } else {
            $audiovisuales=Audiovisual::All()->paginate(5);
        }
        $relaciones = $request->relations; //  Aqui se accede al objeto relations que viene por parámetros
        //  En este bloque If se verifica que exista un objeto relaciones, en caso de que exista se accede a la primera posición del arreglo, si es all lo que contiene entonces se devuelven todas las relaciones para el modelo en cuestión.
        if ($relaciones) {
            if ($relaciones[0] === 'all') {
                $i=0;
                $length = count($audiovisuales);
                for ($i ; $i < $length; $i++) {
                    $audiovisuales[$i]->entrevistados;
                    $audiovisuales[$i]->realizadores;
                    $audiovisuales[$i]->elementos;
                }
            }
            else {
                $i=0;;
                $lengthAudiovisuales = count($audiovisuales);
                $lengthRelaciones = count($relaciones);
                for ($i; $i < $lengthAudiovisuales; $i++) {
                    for ($j=0; $j < $lengthRelaciones; $j++) {
                        $relacionesMetodo = (string)$relaciones[$j];
                        $audiovisuales[$i]->$relacionesMetodo;
                    }
                }
            }
        }
        return response()->json($audiovisuales);
    }

    public function nomenclators()  // Nomenclators | Método que carga los Nomencladores en el Modelo:Audiovisual
    {
        // Sección de Carga de Nomencladores a emplear en las vista
        $clasfAudiov= Vocabulario::findorFail(3)->terminos;  // Nomenclador: Clasificación Audiovisual
        $genAudiov= Vocabulario::findorFail(15)->terminos;  // Nomenclador: Géneros Audiovisuales
        $anos= Vocabulario::findorFail(2)->terminos;  // Nomenclador: Años
        $paises= Vocabulario::findorFail(23)->terminos;  // Nomenclador: Países
        $territorio= Vocabulario::findorFail(36)->terminos;  // Nomenclador: Territorios
        $idiom= Vocabulario::findorFail(18)->terminos;  // Nomenclador: Idiomas
        $idiomSubt= Vocabulario::findorFail(19)->terminos;  // Nomenclador: Idiomas de Subtitulos
        $etiquetas= Vocabulario::findorFail(11)->terminos;  // Nomenclador: Etiquetas
        $nacionalid= Vocabulario::findorFail(22)->terminos;  // Nomenclador: Nacionalidades
        $estdContract= Vocabulario::findorFail(8)->terminos;  // Nomenclador: Estados Contractuales
        return response()->json($clasfAudiov,$genAudiov,$anos,$paises,$territorio,$idiom,$idiomSubt,$etiquetas,$nacionalid,$estdContract);  // Se envian las variables
    }

    public function store(Request $request)  // Store | Método que Guarda el Registro creado en el Modelo:Audiovisual
    {
        return response()->json(Audiovisual::create($request->all()));
    }

    public function update(Request $request,Audiovisual $audiovisual, $id)  // Update | Método que Actualiza un Registro Específico del Modelo:Audiovisual
    {
        return response()->json(Audiovisual::findOrFail($id)->update($request->all()));
    }

    public function destroyLog(Audiovisual $audiovisual, $id)  // DestroyLog | Método que Elimina de forma Lógica un Registro Específico del Modelo:Audiovisual
    {
        return response()->json(Audiovisual::findOrFail($id)->delete());
    }

    public function destroyFis(Audiovisual $audiovisual, $id)  // DestroyFis | Método que Elimina de forma Física un Registro Específico del Modelo:Audiovisual
    {
        return response()->json(Audiovisual::findOrFail($id)->forceDelete());
    }

    public function restoreLog(Audiovisual $audiovisual, $id)  // RestoreLog | Método que Restaura un Registro Específico, eliminado de forma Lógica del Modelo:Audiovisual
    {
        return response()->json(Audiovisual::findOrFail($id)->restore());
    }
}

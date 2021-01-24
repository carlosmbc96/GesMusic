<?php

namespace App\Http\Controllers;

use App\Track;
use App\Vocabulario;
use Illuminate\Http\Request;

class TrackController extends Controller
{
    public function index(Request $request)  // Index | Método que lista todos los Registros del Modelo:Track
    {
        $valorbuscado = $request->valorbuscado;
        $atributo = $request->atributo;
        if ( ($atributo) && ($valorbuscado) ) {
            $tracks=Track::BusqSelect($atributo, $valorbuscado)->paginate(5);
        } else if($valorbuscado){
            $tracks=Track::BusqGeneral($valorbuscado)->paginate(5);
        } else {
            $tracks=Track::withTrashed()->get();
        }
        $relaciones = $request->relations; //  Aqui se accede al objeto relations que viene por parámetros
        //  En este bloque If se verifica que exista un objeto relaciones, en caso de que exista se accede a la primera posición del arreglo, si es all lo que contiene entonces se devuelven todas las relaciones para el modelo en cuestión.
        if ($relaciones) {
            if ($relaciones[0] === 'all') {
                $i=0;
                $length = count($tracks);
                for ($i ; $i < $length; $i++) {
                    $tracks[$i]->temas;
                }
            }
            else {
                $i=0;;
                $lengthTracks = count($tracks);
                $lengthRelaciones = count($relaciones);
                for ($i; $i < $lengthTracks; $i++) {
                    for ($j=0; $j < $lengthRelaciones; $j++) {
                        $relacionesMetodo = (string)$relaciones[$j];
                        $tracks[$i]->$relacionesMetodo;
                    }
                }
            }
        }
        return response()->json($tracks);
    }

    public function nomenclators()  // Nomenclators | Método que carga los Nomencladores en el Modelo:Track
    {
        // Sección de Carga de Nomencladores a emplear en las vista
        $genMusic= Vocabulario::findorFail(16)->terminos;  // Nomenclador: Géneros Musicales
        $SubgMusic= Vocabulario::findorFail(31)->terminos;  // Nomenclador: Subgéneros Musicales
        $mood= Vocabulario::findorFail(20)->terminos;  // Nomenclador: Mood
        $gesTrack= Vocabulario::findorFail(17)->terminos;  // Nomenclador: Gestión de Track
        $paises= Vocabulario::findorFail(23)->terminos;  // Nomenclador: Países
        return response()->json([[$genMusic],[$SubgMusic],[$mood],[$gesTrack],[$paises]]);  // Se envian las variables
    }

    public function store(Request $request)  // Store | Método que Guarda el Registro creado en el Modelo:Track
    {
        return response()->json(Track::create($request->all()));
    }

    public function update(Request $request,Track $track, $id)  // Update | Método que Actualiza un Registro Específico del Modelo:Track
    {
        return response()->json(Track::findOrFail($id)->update($request->all()));
    }

    public function destroyLog(Track $track, $id)  // DestroyLog | Método que Elimina de forma Lógica un Registro Específico del Modelo:Track
    {
        return response()->json(Track::findOrFail($id)->delete());
    }

    public function destroyFis(Track $track, $id)  // DestroyFis | Método que Elimina de forma Física un Registro Específico del Modelo:Track
    {
        return response()->json(Track::findOrFail($id)->forceDelete());
    }

    public function restoreLog(Track $track, $id)  // RestoreLog | Método que Restaura un Registro Específico, eliminado de forma Lógica del Modelo:Track
    {
        return response()->json(Track::findOrFail($id)->restore());
    }
}

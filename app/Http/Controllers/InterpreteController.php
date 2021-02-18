<?php

namespace App\Http\Controllers;

use App\Audiovisual;
use App\Vocabulario;
use App\Audiovisual_Interprete;
use App\Track_Interprete;
use App\Interprete;
use App\Track;
use Illuminate\Http\Request;

class InterpreteController extends Controller
{
    public function index(Request $request)  // Index | Método que lista todos los Registros del Modelo:Interprete
    {
        $valorbuscado = $request->valorbuscado;
        $atributo = $request->atributo;
        if (($atributo) && ($valorbuscado)) {
            $interpretes = Interprete::withTrashed()->BusqSelect($atributo, $valorbuscado)->paginate(5);
        } else if ($valorbuscado) {
            $interpretes = Interprete::withTrashed()->BusqGeneral($valorbuscado)->paginate(5);
        } else {
            $interpretes = Interprete::withTrashed()->get();
        }
        $relaciones = $request->relations; //  Aqui se accede al objeto relations que viene por parámetros
        //  En este bloque If se verifica que exista un objeto relaciones, en caso de que exista se accede a la primera posición del arreglo, si es all lo que contiene entonces se devuelven todas las relaciones para el modelo en cuestión.
        if ($relaciones) {
            if ($relaciones[0] === 'all') {
                $i = 0;
                $length = count($interpretes);
                for ($i; $i < $length; $i++) {
                    $interpretes[$i]->artisticos;
                }
            } else {
                $i = 0;;
                $lengthInterpretes = count($interpretes);
                $lengthRelaciones = count($relaciones);
                for ($i; $i < $lengthInterpretes; $i++) {
                    for ($j = 0; $j < $lengthRelaciones; $j++) {
                        $relacionesMetodo = (string)$relaciones[$j];
                        $interpretes[$i]->$relacionesMetodo;
                    }
                }
            }
        }
        return response()->json($interpretes);
    }
    public function nomenclators()  // Nomenclators | Método que carga los Nomencladores en el Modelo:Proyecto
    {
        // Sección de Carga de Nomencladores a emplear en las vista
        $roles = Vocabulario::findorFail(26)->terminos;  // Nomenclador: Años
        return response()->json($roles);  // Se envian las variables
    }
    public function store(Request $request)  // Store | Método que Guarda el Registro creado en el Modelo:Interprete
    {
        $interprete = Interprete::create([
            "codigInterp" => $request->codigInterp,
            "nombreInterp" => $request->nombreInterp,
            "reseñaBiogInterp" => $request->reseñaBiogInterp
        ]);
        if ($request->type_relation === "audiovisuales") {
            if ($request->audiovisual_id !== "undefined") {
                $audiovisuales = explode(",", $request->audiovisual_id);
                foreach ($audiovisuales as $audiovisual) {
                    Audiovisual_Interprete::create([
                        "interprete_id" => $interprete->id,
                        "audiovisual_id" => $audiovisual
                    ]);
                }
            }
        } else if ($request->type_relation === "tracks") {
            if ($request->track_id !== "undefined") {
                $tracks = explode(",", $request->track_id);
                foreach ($tracks as $track) {
                    Track_Interprete::create([
                        "interprete_id" => $interprete->id,
                        "track_id" => $track
                    ]);
                }
            }
        }
        return response()->json($interprete);
    }

    public function update(Request $request)  // Update | Método que Actualiza un Registro Específico del Modelo:Interprete
    {
        return response()->json(Interprete::findOrFail($request->id)->update($request->all()));
    }

    public function destroyLog($id)  // DestroyLog | Método que Elimina de forma Lógica un Registro Específico del Modelo:Interprete
    {
        return response()->json(Interprete::findOrFail($id)->delete());
    }

    public function destroyFis($id)  // DestroyFis | Método que Elimina de forma Física un Registro Específico del Modelo:Interprete
    {
        $interprete = Interprete::withTrashed()->findOrFail($id);
        if (count($interprete->tracks()->withTrashed()->get()) !== 0) {
            for ($i = count($interprete->tracks()->withTrashed()->get()) - 1; $i >= 0; $i--) {
                $interprete->tracks()->withTrashed()->get()[$i]->pivot->delete();
            }
        }
        if (count($interprete->audiovisuales()->withTrashed()->get()) !== 0) {
            for ($i = count($interprete->audiovisuales()->withTrashed()->get()) - 1; $i >= 0; $i--) {
                $interprete->audiovisuales()->withTrashed()->get()[$i]->pivot->delete();
            }
        }
        return response()->json(Interprete::withTrashed()->findOrFail($id)->forceDelete());
    }

    public function restoreLog($id)  // RestoreLog | Método que Restaura un Registro Específico, eliminado de forma Lógica del Modelo:Interprete
    {
        return response()->json(Interprete::onlyTrashed()->findOrFail($id)->restore());
    }
    public function actualizarRelacionesInterp(Request $request)
    {
        if ($request->relation === "audiovisuales") {
            $audiovisual = Audiovisual::withTrashed()->findOrFail($request->id);
            for ($i = count($audiovisual->interpretes()->withTrashed()->get()) - 1; $i >= 0; $i--) {
                $audiovisual->interpretes()->withTrashed()->get()[$i]->pivot->delete();
            }
            foreach ($request->interpretes as $interprete) {
                Audiovisual_Interprete::create([
                    "audiovisual_id" => $request->id,
                    "interprete_id" => $interprete
                ]);
            }
            return response()->json($audiovisual);
        } else if ($request->relation === "tracks") {
            $track = Track::withTrashed()->findOrFail($request->id);
            for ($i = count($track->interpretes()->withTrashed()->get()) - 1; $i >= 0; $i--) {
                $track->interpretes()->withTrashed()->get()[$i]->pivot->delete();
            }
            foreach ($request->interpretes as $interprete) {
                Track_Interprete::create([
                    "track_id" => $request->id,
                    "interprete_id" => $interprete
                ]);
            }
            return response()->json($track);
        }
    }
    public function interpretesRelacionados(Request $request)  // RestoreLog | Método que Restaura un Registro Específico, eliminado de forma Lógica del Modelo:Fonograma
    {
        $interpretes = [];
        $roles = [];
        if ($request->type_relation === "audiovisuales") {
            $audiovisuales_interpretes = Audiovisual_Interprete::all();
            for ($i = 0; $i < count($audiovisuales_interpretes); $i++) {
                if ($audiovisuales_interpretes[$i]->audiovisual_id === $request->id_relation) {
                    array_push($interpretes, Interprete::withTrashed()->findOrFail($audiovisuales_interpretes[$i]->interprete_id));
                }
            }
            foreach ($interpretes as $interprete) {
                $interprete->audiovisuales;
            }
        } else if ($request->type_relation === "tracks") {
            $tracks_interpretes = Track_Interprete::all();
            for ($i = 0; $i < count($tracks_interpretes); $i++) {
                if ($tracks_interpretes[$i]->track_id === $request->id_relation) {
                    array_push($interpretes, Interprete::withTrashed()->findOrFail($tracks_interpretes[$i]->interprete_id));
                    array_push($roles, $tracks_interpretes[$i]->rolInterp);
                }
            }
            foreach ($interpretes as $interprete) {
                $interprete->tracks;
            }
        }
        return response()->json([$interpretes, $roles]);
    }
}

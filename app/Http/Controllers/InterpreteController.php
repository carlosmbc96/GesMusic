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
                    $interpretes[$i]->audiovisuales;
                    $interpretes[$i]->tracks;
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
                        "rolInterp" => $request->roles,
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
                        "rolInterp" => $request->roles,
                        "track_id" => $track
                    ]);
                }
            }
        }
        return response()->json($interprete);
    }

    public function update(Request $request)  // Update | Método que Actualiza un Registro Específico del Modelo:Interprete
    {
        $interprete = Interprete::withTrashed()->findOrFail($request->id);
        $interprete->update([
            "codigInterp" => $request->codigInterp,
            "nombreInterp" => $request->nombreInterp,
            "reseñaBiogInterp" => $request->reseñaBiogInterp
        ]);
        if ($request->type_relation === "audiovisuales") {
            if ($request->audiovisual_id !== "undefined") {
                $audiovisuales = explode(",", $request->audiovisual_id);
                $aud_interp = [];
                $audiovisuales_interpretes = Audiovisual_Interprete::all();
                for ($i = 0; $i < count($audiovisuales_interpretes); $i++) {
                    if (($audiovisuales_interpretes[$i]->audiovisual_id === intval($audiovisuales[0])) && ($audiovisuales_interpretes[$i]->interprete_id === $interprete->id)) {
                        array_push($aud_interp, $audiovisuales_interpretes[$i]);
                    }
                }
                $aud_interp[0]->update([
                    "interprete_id" => $interprete->id,
                    "rolInterp" => $request->roles,
                    "audiovisual_id" => $audiovisuales[0]
                ]);
            }
        } else if ($request->type_relation === "tracks") {
            if ($request->track_id !== "undefined") {
                $tracks = explode(",", $request->track_id);
                $trk_interp = [];
                $tracks_interpretes = Track_Interprete::all();
                for ($i = 0; $i < count($tracks_interpretes); $i++) {
                    if (($tracks_interpretes[$i]->track_id === intval($tracks[0])) && ($tracks_interpretes[$i]->interprete_id === $interprete->id)) {
                        array_push($trk_interp, $tracks_interpretes[$i]);
                    }
                }
                $trk_interp[0]->update([
                    "interprete_id" => $interprete->id,
                    "rolInterp" => $request->roles,
                    "track_id" => $tracks[0]
                ]);
            }
        }
        return response()->json($interprete);
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
        $old_relations = [];
        $new_relations = [];
        $founded = false;
        $interpretes = $request->interpretes;
        if ($request->relation === "audiovisuales") {
            $audiovisual = Audiovisual::withTrashed()->findOrFail($request->id);
            $all_realtions = Audiovisual_Interprete::all();
            for ($i = 0; $i < count($interpretes); $i++) {
                for ($j = 0; $j < count($all_realtions); $j++) {
                    if (($all_realtions[$j]->audiovisual_id === $audiovisual->id) && ($all_realtions[$j]->interprete_id === intval($interpretes[$i]))) {
                        array_push($old_relations, $all_realtions[$j]);
                        $founded = true;
                        break;
                    }
                }
                if (!$founded) {
                    array_push($new_relations, $interpretes[$i]);
                } else $founded = false;
            }
            for ($k = count($audiovisual->interpretes()->withTrashed()->get()) - 1; $k >= 0; $k--) {
                $audiovisual->interpretes()->withTrashed()->get()[$k]->pivot->delete();
            }
            var_dump($old_relations);
            foreach ($old_relations as $old_interprete) {
                Audiovisual_Interprete::create([
                    "audiovisual_id" => $old_interprete->audiovisual_id,
                    "rolInterp" => $old_interprete->rolInterp,
                    "interprete_id" => $old_interprete->interprete_id
                ]);
            }
            foreach ($new_relations as $interprete) {
                Audiovisual_Interprete::create([
                    "audiovisual_id" => $audiovisual->id,
                    "interprete_id" => $interprete
                ]);
            }
            return response()->json($audiovisual);
        } else if ($request->relation === "tracks") {
            $track = Track::withTrashed()->findOrFail($request->id);
            $all_realtions = Track_Interprete::all();
            for ($i = 0; $i < count($interpretes); $i++) {
                for ($j = 0; $j < count($all_realtions); $j++) {
                    if (($all_realtions[$j]->track_id === $track->id) && ($all_realtions[$j]->interprete_id === intval($interpretes[$i]))) {
                        array_push($old_relations, $all_realtions[$j]);
                        $founded = true;
                        break;
                    }
                }
                if (!$founded) {
                    array_push($new_relations, $interpretes[$i]);
                } else $founded = false;
            }
            for ($k = count($track->interpretes()->withTrashed()->get()) - 1; $k >= 0; $k--) {
                $track->interpretes()->withTrashed()->get()[$k]->pivot->delete();
            }
            foreach ($old_relations as $old_interprete) {
                Track_Interprete::create([
                    "track_id" => $old_interprete->track_id,
                    "rolInterp" => $old_interprete->rolInterp,
                    "interprete_id" => $old_interprete->interprete_id
                ]);
            }
            foreach ($new_relations as $interprete) {
                Track_Interprete::create([
                    "track_id" => $track->id,
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
                    array_push($roles, $audiovisuales_interpretes[$i]->rolInterp);
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

<?php

namespace App\Http\Controllers;

use App\Autor;
use App\Tema;
use App\Tema_Autor;
use App\Vocabulario;
use Illuminate\Http\Request;

class TemaController extends Controller
{
    public function index(Request $request)  // Index | Método que lista todos los Registros del Modelo:Tema
    {
        $valorbuscado = $request->valorbuscado;
        $atributo = $request->atributo;
        if (($atributo) && ($valorbuscado)) {
            $temas = Tema::withTrashed()->BusqSelect($atributo, $valorbuscado)->get();
        } else if ($valorbuscado) {
            $temas = Tema::withTrashed()->BusqGeneral($valorbuscado)->get();
        } else {
            $temas = Tema::withTrashed()->get();
        }
        $relaciones = $request->relations; //  Aqui se accede al objeto relations que viene por parámetros
        //  En este bloque If se verifica que exista un objeto relaciones, en caso de que exista se accede a la primera posición del arreglo, si es all lo que contiene entonces se devuelven todas las relaciones para el modelo en cuestión.
        if ($relaciones) {
            if ($relaciones[0] === 'all') {
                $i = 0;
                $length = count($temas);
                for ($i; $i < $length; $i++) {
                    $temas[$i]->tracks;
                    $temas[$i]->autores;
                }
            } else {
                $i = 0;;
                $lengthTemas = count($temas);
                $lengthRelaciones = count($relaciones);
                for ($i; $i < $lengthTemas; $i++) {
                    for ($j = 0; $j < $lengthRelaciones; $j++) {
                        $relacionesMetodo = (string)$relaciones[$j];
                        $temas[$i]->$relacionesMetodo;
                    }
                }
            }
        }
        return response()->json($temas);
    }

    public function store(Request $request)  // Store | Método que Guarda el Registro creado en el Modelo:Tema
    {
        $tema = Tema::create([
            "codigTema" => $request->codigTema,
            "tituloTem" => $request->tituloTem,
            "catalDigitalTem" => $request->catalDigitalTem,
            "sociedadGestionTem" => $request->sociedadGestionTem,
            "descripTem" => $request->descripTem,
            "track_id" => $request->track_id,
        ]);
        if ($request->type_relation === "autores") {
            if ($request->autor_id !== "undefined") {
                $autores = explode(",", $request->autor_id);
                foreach ($autores as $autor) {
                    Tema_Autor::create([
                        "tema_id" => $tema->id,
                        "autor_id" => $autor
                    ]);
                }
            }
        }
        $tema->save();
        return response()->json($tema);
    }

    public function update(Request $request)  // Update | Método que Actualiza un Registro Específico del Modelo:Tema
    {
        $tema = Tema::withTrashed()->findOrFail($request->id);
        $tema->update([
            "codigTema" => $request->codigTema,
            "tituloTem" => $request->tituloTem,
            "catalDigitalTem" => $request->catalDigitalTem,
            "sociedadGestionTem" => $request->sociedadGestionTem,
            "descripTem" => $request->descripTem,
            "track_id" => $request->track_id,
        ]);
        return response()->json($tema);
    }

    public function destroyLog($id)  // DestroyLog | Método que Elimina de forma Lógica un Registro Específico del Modelo:Tema
    {
        return response()->json(Tema::findOrFail($id)->delete());
    }

    public function destroyFis(Tema $tema, $id)  // DestroyFis | Método que Elimina de forma Física un Registro Específico del Modelo:Tema
    {
        $tema = Tema::withTrashed()->findOrFail($id);
        if (count($tema->autores()->withTrashed()->get()) !== 0) {
            for ($i = count($tema->autores()->withTrashed()->get()) - 1; $i >= 0; $i--) {
                $tema->autores()->withTrashed()->get()[$i]->pivot->delete();
            }
        }
        return response()->json($tema->forceDelete());
    }

    public function restoreLog($id)  // RestoreLog | Método que Restaura un Registro Específico, eliminado de forma Lógica del Modelo:Tema
    {
        return response()->json(Tema::onlyTrashed()->findOrFail($id)->restore());
    }

    public function nomenclators()  // Nomenclators | Método que carga los Nomencladores en el Modelo:Track
    {
        // Sección de Carga de Nomencladores a emplear en las vista
        $sociedadGestionTem = Vocabulario::findorFail(29)->terminos;
        return response()->json($sociedadGestionTem);  // Se envian las variables
    }
    public function actualizarRelacionesTem(Request $request)
    {
        if ($request->relation === "autores") {
            $autor = Autor::withTrashed()->findOrFail($request->id);
            for ($i = count($autor->temas()->withTrashed()->get()) - 1; $i >= 0; $i--) {
                $autor->temas()->withTrashed()->get()[$i]->pivot->delete();
            }
            foreach ($request->temas as $tema) {
                Tema_Autor::create([
                    "autor_id" => $request->id,
                    "tema_id" => $tema
                ]);
            }
            return response()->json($autor);
        }
    }
}

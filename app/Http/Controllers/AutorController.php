<?php

namespace App\Http\Controllers;

use App\Autor;
use App\Audiovisual;
use App\Track;
use App\Audiovisual_Autor;
use App\Tema_Autor;
use App\Track_Autor;
use App\Vocabulario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AutorController extends Controller
{
    public function index(Request $request)  // Index | Método que lista todos los Registros del Modelo:Autor
    {
        $valorbuscado = $request->valorbuscado;
        $atributo = $request->atributo;
        if (($atributo) && ($valorbuscado)) {
            $autores = Autor::withTrashed()->BusqSelect($atributo, $valorbuscado)->get();
        } else if ($valorbuscado) {
            $autores = Autor::withTrashed()->BusqGeneral($valorbuscado)->get();
        } else {
            $autores = Autor::withTrashed()->get();
        }
        $relaciones = $request->relations; //  Aqui se accede al objeto relations que viene por parámetros
        //  En este bloque If se verifica que exista un objeto relaciones, en caso de que exista se accede a la primera posición del arreglo, si es all lo que contiene entonces se devuelven todas las relaciones para el modelo en cuestión.
        if ($relaciones) {
            if ($relaciones[0] === 'all') {
                $i = 0;
                $length = count($autores);
                for ($i; $i < $length; $i++) {
                }
            } else {
                $i = 0;;
                $lengthAutores = count($autores);
                $lengthRelaciones = count($relaciones);
                for ($i; $i < $lengthAutores; $i++) {
                    for ($j = 0; $j < $lengthRelaciones; $j++) {
                        $relacionesMetodo = (string)$relaciones[$j];
                        $autores[$i]->$relacionesMetodo;
                    }
                }
            }
        }
        return response()->json($autores);
    }

    public function nomenclators()  // Nomenclators | Método que carga los Nomencladores en el Modelo:Autor
    {
        // Sección de Carga de Nomencladores a emplear en las vista
        $sexos = Vocabulario::findorFail(28)->terminos;  // Nomenclador: Sexos
        return response()->json($sexos);  // Se envian las variables
    }

    public function store(Request $request)  // Store | Método que Guarda el Registro creado en el Modelo:Autor
    {
        $autor = Autor::create([
            "ciAutr" => $request->ciAutr,
            "codigAutr" => $request->codigAutr,
            "nombresAutr" => $request->nombresAutr,
            "apellidosAutr" => $request->apellidosAutr,
            "sexoAutr" => $request->sexoAutr,
            "fallecidoAutr" => $request->fallecidoAutr,
            "obrasCatEditAutr" => $request->obrasCatEditAutr,
            "reseñaBiogAutr" => $request->reseñaBiogAutr,
        ]);
        if ($request->type_relation === "audiovisuales") {
            if ($request->audiovisual_id !== "undefined") {
                $audiovisuales = explode(",", $request->audiovisual_id);
                foreach ($audiovisuales as $audiovisual) {
                    Audiovisual_Autor::create([
                        "autor_id" => $autor->id,
                        "audiovisual_id" => $audiovisual
                    ]);
                }
            }
        } else if ($request->type_relation === "tracks") {
            if ($request->track_id !== "undefined") {
                $tracks = explode(",", $request->track_id);
                foreach ($tracks as $track) {
                    Track_Autor::create([
                        "autor_id" => $autor->id,
                        "track_id" => $track
                    ]);
                }
            }
        } else  if ($request->type_relation === "temas") {
            if ($request->tema_id !== "undefined") {
                $temas = explode(",", $request->tema_id);
                foreach ($temas as $tema) {
                    Tema_Autor::create([
                        "autor_id" => $autor->id,
                        "tema_id" => $tema
                    ]);
                }
            }
        }
        if ($request->fotoAutr !== null) {
            $autor->setFotoAutrAttribute($request->fotoAutr, $request->codigAutr);
        } else
            $autor->setFotoAutrAttributeDefault();
        $autor->save();
        return response()->json($autor);
    }

    public function update(Request $request)  // Update | Método que Actualiza un Registro Específico del Modelo:Autor
    {
        $autor = Autor::findOrFail($request->id);
        if ($request->fotoAutr !== null) {
            if (substr($autor->fotoAutr, 36) !== "Logo ver vertical_Ltr Negras.png") {
                Storage::disk('local')->delete('/Imagenes/Artistas/Autores/' . substr($autor->fotoAutr, 36));
            }
            $autor->setFotoAutrAttribute($request->fotoAutr, $request->codigAutr);
        } else if ($request->img_default) {
            Storage::disk('local')->delete('/Imagenes/Artistas/Autores/' . substr($autor->fotoAutr, 36));
            $autor->setFotoAutrAttributeDefault();
        }
        $autor->update([
            "ciAutr" => $request->ciAutr,
            "codigAutr" => $request->codigAutr,
            "nombresAutr" => $request->nombresAutr,
            "apellidosAutr" => $request->apellidosAutr,
            "sexoAutr" => $request->sexoAutr,
            "fallecidoAutr" => $request->fallecidoAutr,
            "obrasCatEditAutr" => $request->obrasCatEditAutr,
            "reseñaBiogAutr" => $request->reseñaBiogAutr,
        ]);
        return response()->json($autor);
    }

    public function destroyLog($id)  // DestroyLog | Método que Elimina de forma Lógica un Registro Específico del Modelo:Autor
    {
        return response()->json(Autor::findOrFail($id)->delete());
    }

    public function destroyFis($id)  // DestroyFis | Método que Elimina de forma Física un Registro Específico del Modelo:Autor
    {
        $autor = Autor::withTrashed()->findOrFail($id);
        if (count($autor->audiovisuales()->withTrashed()->get()) !== 0) {
            for ($i = count($autor->audiovisuales()->withTrashed()->get()) - 1; $i >= 0; $i--) {
                $autor->audiovisuales()->withTrashed()->get()[$i]->pivot->delete();
            }
        }
        if (count($autor->tracks()->withTrashed()->get()) !== 0) {
            for ($i = count($autor->tracks()->withTrashed()->get()) - 1; $i >= 0; $i--) {
                $autor->tracks()->withTrashed()->get()[$i]->pivot->delete();
            }
        }
        if (count($autor->temas()->withTrashed()->get()) !== 0) {
            for ($i = count($autor->temas()->withTrashed()->get()) - 1; $i >= 0; $i--) {
                $autor->temas()->withTrashed()->get()[$i]->pivot->delete();
            }
        }
        if (substr($autor->fotoAutr, 36) !== "Logo ver vertical_Ltr Negras.png") {
            Storage::disk('local')->delete('/Imagenes/Artistas/Autores/' . substr($autor->fotoAutr, 36));
        }
        return response()->json($autor->forceDelete());
    }

    public function restoreLog($id)  // RestoreLog | Método que Restaura un Registro Específico, eliminado de forma Lógica del Modelo:Autor
    {
        return response()->json(Autor::onlyTrashed()->findOrFail($id)->restore());
    }
    public function actualizarRelacionesAut(Request $request)
    {
        if ($request->relation === "audiovisuales") {
            $audiovisual = Audiovisual::withTrashed()->findOrFail($request->id);
            for ($i = count($audiovisual->autores()->withTrashed()->get()) - 1; $i >= 0; $i--) {
                $audiovisual->autores()->withTrashed()->get()[$i]->pivot->delete();
            }
            foreach ($request->autores as $autor) {
                Audiovisual_Autor::create([
                    "audiovisual_id" => $request->id,
                    "autor_id" => $autor
                ]);
            }
            return response()->json($audiovisual);
        } else if ($request->relation === "tracks") {
            $track = Track::withTrashed()->findOrFail($request->id);
            for ($i = count($track->autores()->withTrashed()->get()) - 1; $i >= 0; $i--) {
                $track->autores()->withTrashed()->get()[$i]->pivot->delete();
            }
            foreach ($request->autores as $autor) {
                Track_Autor::create([
                    "track_id" => $request->id,
                    "autor_id" => $autor
                ]);
            }
            return response()->json($track);
        }
    }

		public function temas(Request $request) {
			$autor = Autor::withTrashed()->findOrFail($request->id);
			for ($i = count($autor->temas()->withTrashed()->get()) - 1; $i >= 0; $i--) {
				$autor->temas()->withTrashed()->get()[$i]->pivot->delete();
			}
			foreach ($request->temas as $tema) {
				Tema_Autor::create([
					"autor_id" => $request->id,
					"tema_id" => $tema,
				]);
			}
			return response()->json($autor);
		}
}

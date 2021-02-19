<?php

namespace App\Http\Controllers;

use App\Fonograma;
use App\Fonograma_Track;
use App\Producto;
use App\Proyecto;
use App\Track;
use App\Track_Autor;
use App\Track_Interprete;
use App\Vocabulario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TrackController extends Controller
{
    public function index(Request $request)  // Index | Método que lista todos los Registros del Modelo:Track
    {
        $valorbuscado = $request->valorbuscado;
        $atributo = $request->atributo;
        if (($atributo) && ($valorbuscado)) {
            $tracks = Track::withTrashed()->BusqSelect($atributo, $valorbuscado)->get();
        } else if ($valorbuscado) {
            $tracks = Track::withTrashed()->BusqGeneral($valorbuscado)->get();
        } else {
            $tracks = Track::withTrashed()->get();
        }
        $relaciones = $request->relations; //  Aqui se accede al objeto relations que viene por parámetros
        //  En este bloque If se verifica que exista un objeto relaciones, en caso de que exista se accede a la primera posición del arreglo, si es all lo que contiene entonces se devuelven todas las relaciones para el modelo en cuestión.
        if ($relaciones) {
            if ($relaciones[0] === 'all') {
                $i = 0;
                $length = count($tracks);
                for ($i; $i < $length; $i++) {
                    $tracks[$i]->temas;
                    $tracks[$i]->fonogramas;
                    $tracks[$i]->autores;
                    $tracks[$i]->interpretes;
                }
            } else {
                $i = 0;;
                $lengthTracks = count($tracks);
                $lengthRelaciones = count($relaciones);
                for ($i; $i < $lengthTracks; $i++) {
                    for ($j = 0; $j < $lengthRelaciones; $j++) {
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
        $genMusic = Vocabulario::findorFail(16)->terminos;  // Nomenclador: Géneros Musicales
        $SubgMusic = Vocabulario::findorFail(31)->terminos;  // Nomenclador: Subgéneros Musicales
        $mood = Vocabulario::findorFail(20)->terminos;  // Nomenclador: Mood
        $gesTrack = Vocabulario::findorFail(17)->terminos;  // Nomenclador: Gestión de Track
        $paises = Vocabulario::findorFail(23)->terminos;  // Nomenclador: Países
				$rolesInterp = Vocabulario::findorFail(26)->terminos;  // Nomenclador: Roles de Intérpretes
        return response()->json([[$genMusic], [$SubgMusic], [$mood], [$gesTrack], [$paises], [$rolesInterp]]);  // Se envian las variables
    }

    public function store(Request $request)  // Store | Método que Guarda el Registro creado en el Modelo:Track
    {
        $track = Track::create([
            "tituloTrk" => $request->tituloTrk,
            "isrcTrk" => $request->isrcTrk,
            "duracionTrk" => $request->duracionTrk,
            "muestraTrk" => $request->muestraTrk,
            "envivoTrk" => $request->envivoTrk,
            "generoTrk" => $request->generoTrk,
            "bonusTrk" => $request->bonusTrk,
            "moodTrk" => $request->moodTrk,
            "gestionTrk" => $request->gestionTrk,
            "paisgrabTrk" => $request->paisgrabTrk,
            "archivoTrk" => $request->archivoTrk,
            "archivoMuestraTrk" => $request->archivoMuestraTrk,
            "dirArbolTrk" => $request->dirArbolTrk,
        ]);
        if ($request->fonograma_id !== "undefined") {
            $fonogramas = explode(",", $request->fonograma_id);
            foreach ($fonogramas as $fonograma) {
                Fonograma_Track::create([
                    "fonograma_id" => $fonograma,
                    "track_id" => $track->id
                ]);
            }
            $this->crearDirectorios($fonogramas);
        }
        $track->save();
        return response()->json($track);
    }

    public function update(Request $request)  // Update | Método que Actualiza un Registro Específico del Modelo:Fonograma
    {
        $track = Track::findOrFail($request->id);
        $track->update([
            "tituloTrk" => $request->tituloTrk,
            "isrcTrk" => $request->isrcTrk,
            "duracionTrk" => $request->duracionTrk,
            "muestraTrk" => $request->muestraTrk,
            "envivoTrk" => $request->envivoTrk,
            "generoTrk" => $request->generoTrk,
            "bonusTrk" => $request->bonusTrk,
            "moodTrk" => $request->moodTrk,
            "gestionTrk" => $request->gestionTrk,
            "paisgrabTrk" => $request->paisgrabTrk,
            "archivoTrk" => $request->archivoTrk,
            "archivoMuestraTrk" => $request->archivoMuestraTrk,
            "dirArbolTrk" => $request->dirArbolTrk,
        ]);
        $fonogramasOld = [];
        $igual_cont = 0;
        if ($request->fonograma_id !== null) {
            $fonogramas = explode(",", $request->fonograma_id);
            for ($i = 0; $i < count($track->fonogramas); $i++) {
                array_push($fonogramasOld, $track->fonogramas[$i]->pivot->fonograma_id);
            }
            if (count($fonogramas) === count($track->fonogramas)) {
                for ($i = 0; $i < count($track->fonogramas); $i++) {
                    if ($track->fonogramas[$i]->pivot->fonograma_id === $fonogramas[$i]) {
                        $igual_cont++;
                    }
                }
                if ($igual_cont !== count($fonogramas)) {
                    $this->actualizarRelacion($track, $fonogramas);
                }
            } else {
                $this->actualizarRelacion($track, $fonogramas);
            }
        } else if (count($track->fonogramas) !== 0) {
            for ($i = 0; $i < count($track->fonogramas); $i++) {
                array_push($fonogramasOld, $track->fonogramas[$i]->pivot->fonograma_id);
                $track->fonogramas[$i]->pivot->delete();
            }
        }
        return response()->json($track);
    }

    public function destroyLog($id)
    {
        return response()->json(Track::findOrFail($id)->delete());
    }

    public function destroyFis($id)
    {
        $track = Track::withTrashed()->findOrFail($id);
        for ($i = count($track->fonogramas()->withTrashed()->get()) - 1; $i >= 0; $i--) {
            $track->fonogramas()->withTrashed()->get()[$i]->pivot->delete();
        }
        for ($i = count($track->interpretes()->withTrashed()->get()) - 1; $i >= 0; $i--) {
            $track->interpretes()->withTrashed()->get()[$i]->pivot->delete();
        }
        for ($i = count($track->autores()->withTrashed()->get()) - 1; $i >= 0; $i--) {
            $track->autores()->withTrashed()->get()[$i]->pivot->delete();
        }
        return response()->json($track->forceDelete());
    }

    public function restoreLog($id)
    {
        return response()->json(Track::onlyTrashed()->findOrFail($id)->restore());
    }
    public function crearDirectorios($fonogramas)
    {
        $productos = [];
        for ($i = 0; $i < count($fonogramas); $i++) {
            $fonograma = Fonograma::withTrashed()->findOrFail($fonogramas[$i]);
            for ($j = 0; $j < count($fonograma->productos); $j++) {
                array_push($productos, $fonograma->productos[$j]->pivot->producto_id);
                $producto = Producto::withTrashed()->findOrFail($productos[$j]);
                var_dump($producto->codigProd);
                $proyecto = Proyecto::withTrashed()->findOrFail($producto->proyecto_id);
                Storage::disk('local')->makeDirectory('/Proyectos/' . $proyecto->codigProy . "/" . $producto->codigProd . "/" . $fonograma->codigFong . "/" . "Wav");
                Storage::disk('local')->makeDirectory('/Proyectos/' . $proyecto->codigProy . "/" . $producto->codigProd . "/" . $fonograma->codigFong . "/" . "Mp3");
                Storage::disk('local')->makeDirectory('/Proyectos/' . $proyecto->codigProy . "/" . $producto->codigProd . "/" . $fonograma->codigFong . "/" . "Elementos");
            }
        }
    }
    public function actualizarRelacion($track, $fonogramas)  // RestoreLog | Método que Restaura un Registro Específico, eliminado de forma Lógica del Modelo:Fonograma
    {
        for ($i = 0; $i < count($track->fonogramas); $i++) {
            $track->fonogramas[$i]->pivot->delete();
        }
        foreach ($fonogramas as $fonograma) {
            Fonograma_Track::create([
                "track_id" => $track->id,
                "fonograma_id" => $fonograma
            ]);
        }
    }
    public function tracksRelacionados(Request $request)  // RestoreLog | Método que Restaura un Registro Específico, eliminado de forma Lógica del Modelo:Fonograma
    {
        $tracks = [];
        $fonogramas_tracks = Fonograma_Track::all();
        for ($i = 0; $i < count($fonogramas_tracks); $i++) {
            if ($fonogramas_tracks[$i]->fonograma_id === $request->idFong) {
                array_push($tracks, Track::withTrashed()->findOrFail($fonogramas_tracks[$i]->track_id));
            }
        }
        foreach ($tracks as $track) {
            $track->fonogramas;
        }
        return response()->json($tracks);
    }

		public function autores(Request $request) {
			$track = Track::withTrashed()->findOrFail($request->id);
			for ($i = count($track->autores()->withTrashed()->get()) - 1; $i >= 0; $i--) {
				$track->autores()->withTrashed()->get()[$i]->pivot->delete();
			}
			foreach ($request->autores as $autor) {
				Track_Autor::create([
					"autor_id" => $autor,
					"track_id" => $request->id,
				]);
			}
			return response()->json($track);
		}

		public function interpretes(Request $request) {
			$track = Track::withTrashed()->findOrFail($request->id);
			for ($i = count($track->interpretes()->withTrashed()->get()) - 1; $i >= 0; $i--) {
				$track->interpretes()->withTrashed()->get()[$i]->pivot->delete();
			}
			for ($i = 0; $i < count($request->interpretes); $i++) {
				var_dump($request->interpretes[$i][1]);
				Track_Interprete::create([
						"interprete_id" => $request->interpretes[$i][0],
						"rolInterp" => $request->interpretes[$i][1],
						"track_id" => $request->id
				]);
		}
			return response()->json($track);
		}
}

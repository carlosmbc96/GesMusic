<?php

namespace App\Http\Controllers;

use App\Autor;
use App\Vocabulario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AutorController extends Controller
{
    public function index(Request $request)  // Index | Método que lista todos los Registros del Modelo:Autor
    {
        $valorbuscado = $request->valorbuscado;
        $atributo = $request->atributo;
        if ( ($atributo) && ($valorbuscado) ) {
            $autores=Autor::withTrashed()->BusqSelect($atributo, $valorbuscado)->get();
        } else if($valorbuscado){
            $autores=Autor::withTrashed()->BusqGeneral($valorbuscado)->get();
        } else {
            $autores=Autor::withTrashed()->get();
        }
        $relaciones = $request->relations; //  Aqui se accede al objeto relations que viene por parámetros
        //  En este bloque If se verifica que exista un objeto relaciones, en caso de que exista se accede a la primera posición del arreglo, si es all lo que contiene entonces se devuelven todas las relaciones para el modelo en cuestión.
        if ($relaciones) {
            if ($relaciones[0] === 'all') {
                $i=0;
                $length = count($autores);
                for ($i ; $i < $length; $i++) {
                }
            }
            else {
                $i=0;;
                $lengthAutores = count($autores);
                $lengthRelaciones = count($relaciones);
                for ($i; $i < $lengthAutores; $i++) {
                    for ($j=0; $j < $lengthRelaciones; $j++) {
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
        $sexos= Vocabulario::findorFail(28)->terminos;  // Nomenclador: Sexos
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
                Storage::disk('local')->delete('/Imagenes/Artistas/Autores/'.substr($autor->fotoAutr, 36));
            }
            $autor->setFotoAutrAttribute($request->fotoAutr, $request->codigProy);
        } else if ($request->img_default) {
            Storage::disk('local')->delete('/Imagenes/Artistas/Autores/'.substr($autor->fotoAutr, 36));
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
        if (substr($autor->fotoAutr, 36) !== "Logo ver vertical_Ltr Negras.png") {
            Storage::disk('local')->delete('/Imagenes/Artistas/Autores/'.substr($autor->fotoAutr, 36));
        }
        return response()->json($autor->forceDelete());
    }

    public function restoreLog($id)  // RestoreLog | Método que Restaura un Registro Específico, eliminado de forma Lógica del Modelo:Autor
    {
        return response()->json(Autor::onlyTrashed()->findOrFail($id)->restore());
    }
}

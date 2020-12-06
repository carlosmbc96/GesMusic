<?php

namespace App\Http\Controllers;

use App\Autor;
use Illuminate\Http\Request;

class AutorController extends Controller
{
    public function index(Request $request)  // Index | Método que lista todos los Registros del Modelo:Autor
    {
        $valorbuscado = $request->valorbuscado;
        $atributo = $request->atributo;
        if ( ($atributo) && ($valorbuscado) ) {
            $autores=Autor::BusqSelect($atributo, $valorbuscado)->paginate(5);
        } else if($valorbuscado){
            $autores=Autor::BusqGeneral($valorbuscado)->paginate(5);
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
        return response()->json(Autor::create($request->all()));
    }

    public function update(Request $request,Autor $autor, $id)  // Update | Método que Actualiza un Registro Específico del Modelo:Autor
    {
        return response()->json(Autor::findOrFail($id)->update($request->all()));
    }

    public function destroyLog(Autor $autor, $id)  // DestroyLog | Método que Elimina de forma Lógica un Registro Específico del Modelo:Autor
    {
        return response()->json(Autor::findOrFail($id)->delete());
    }

    public function destroyFis(Autor $autor, $id)  // DestroyFis | Método que Elimina de forma Física un Registro Específico del Modelo:Autor
    {
        return response()->json(Autor::findOrFail($id)->forceDelete());
    }

    public function restoreLog(Autor $autor, $id)  // RestoreLog | Método que Restaura un Registro Específico, eliminado de forma Lógica del Modelo:Autor
    {
        return response()->json(Autor::findOrFail($id)->restore());
    }
}

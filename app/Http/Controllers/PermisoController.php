<?php

namespace App\Http\Controllers;

use App\Permiso;
use Illuminate\Http\Request;

class PermisoController extends Controller
{
    public function index(Request $request)  // Index | Método que lista todos los Registros del Modelo:Permiso
    {
        $valorbuscado = $request->valorbuscado;
        $atributo = $request->atributo;
        if ( ($atributo) && ($valorbuscado) ) {
            $permisos=Permiso::BusqSelect($atributo, $valorbuscado)->paginate(5);
        } else if($valorbuscado){
            $permisos=Permiso::BusqGeneral($valorbuscado)->paginate(5);
        } else {
            $permisos=Permiso::All()->paginate(5);
        }
        $relaciones = $request->relations; //  Aqui se accede al objeto relations que viene por parámetros
        //  En este bloque If se verifica que exista un objeto relaciones, en caso de que exista se accede a la primera posición del arreglo, si es all lo que contiene entonces se devuelven todas las relaciones para el modelo en cuestión.
        if ($relaciones) {
            if ($relaciones[0] === 'all') {
                $i=0;
                $length = count($permisos);
                for ($i ; $i < $length; $i++) {
                }
            }
            else {
                $i=0;;
                $lengthPermisos = count($permisos);
                $lengthRelaciones = count($relaciones);
                for ($i; $i < $lengthPermisos; $i++) {
                    for ($j=0; $j < $lengthRelaciones; $j++) {
                        $relacionesMetodo = (string)$relaciones[$j];
                        $permisos[$i]->$relacionesMetodo;
                    }
                }
            }
        }
        return response()->json($permisos);
    }

    public function store(Request $request)  // Store | Método que Guarda el Registro creado en el Modelo:Permiso
    {
        return response()->json(Permiso::create($request->all()));
    }

    public function update(Request $request,Permiso $permiso, $id)  // Update | Método que Actualiza un Registro Específico del Modelo:Permiso
    {
        return response()->json(Permiso::findOrFail($id)->update($request->all()));
    }

    public function destroyLog(Permiso $permiso, $id)  // DestroyLog | Método que Elimina de forma Lógica un Registro Específico del Modelo:Permiso
    {
        return response()->json(Permiso::findOrFail($id)->delete());
    }

    public function destroyFis(Permiso $permiso, $id)  // DestroyFis | Método que Elimina de forma Física un Registro Específico del Modelo:Permiso
    {
        return response()->json(Permiso::findOrFail($id)->forceDelete());
    }

    public function restoreLog(Permiso $permiso, $id)  // RestoreLog | Método que Restaura un Registro Específico, eliminado de forma Lógica del Modelo:Permiso
    {
        return response()->json(Permiso::findOrFail($id)->restore());
    }
}

<?php

namespace App\Http\Controllers;

use App\Rol;
use Illuminate\Http\Request;

class RolController extends Controller
{
    public function index(Request $request)  // Index | Método que lista todos los Registros del Modelo:Rol
    {
        $valorbuscado = $request->valorbuscado;
        $atributo = $request->atributo;
        if ( ($atributo) && ($valorbuscado) ) {
            $roles=Rol::BusqSelect($atributo, $valorbuscado)->paginate(5);
        } else if($valorbuscado){
            $roles=Rol::BusqGeneral($valorbuscado)->paginate(5);
        } else {
            $roles=Rol::All()->paginate(5);
        }
        $relaciones = $request->relations; //  Aqui se accede al objeto relations que viene por parámetros
        //  En este bloque If se verifica que exista un objeto relaciones, en caso de que exista se accede a la primera posición del arreglo, si es all lo que contiene entonces se devuelven todas las relaciones para el modelo en cuestión.
        if ($relaciones) {
            if ($relaciones[0] === 'all') {
                $i=0;
                $length = count($roles);
                for ($i ; $i < $length; $i++) {
                }
            }
            else {
                $i=0;;
                $lengthRoles = count($roles);
                $lengthRelaciones = count($relaciones);
                for ($i; $i < $lengthRoles; $i++) {
                    for ($j=0; $j < $lengthRelaciones; $j++) {
                        $relacionesMetodo = (string)$relaciones[$j];
                        $roles[$i]->$relacionesMetodo;
                    }
                }
            }
        }
        return response()->json($roles);
    }

    public function store(Request $request)  // Store | Método que Guarda el Registro creado en el Modelo:Rol
    {
        return response()->json(Rol::create($request->all()));
    }

    public function update(Request $request,Rol $rol, $id)  // Update | Método que Actualiza un Registro Específico del Modelo:Rol
    {
        return response()->json(Rol::findOrFail($id)->update($request->all()));
    }

    public function destroyLog(Rol $rol, $id)  // DestroyLog | Método que Elimina de forma Lógica un Registro Específico del Modelo:Rol
    {
        return response()->json(Rol::findOrFail($id)->delete());
    }

    public function destroyFis(Rol $rol, $id)  // DestroyFis | Método que Elimina de forma Física un Registro Específico del Modelo:Rol
    {
        return response()->json(Rol::findOrFail($id)->forceDelete());
    }

    public function restoreLog(Rol $rol, $id)  // RestoreLog | Método que Restaura un Registro Específico, eliminado de forma Lógica del Modelo:Rol
    {
        return response()->json(Rol::findOrFail($id)->restore());
    }
}

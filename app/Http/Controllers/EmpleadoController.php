<?php

namespace App\Http\Controllers;

use App\Empleado;
use Illuminate\Http\Request;

class EmpleadoController extends Controller
{
    public function index(Request $request)  // Index | Método que lista todos los Registros del Modelo:Empleado
    {
        $valorbuscado = $request->valorbuscado;
        $atributo = $request->atributo;
        if ( ($atributo) && ($valorbuscado) ) {
            $empleados=Empleado::BusqSelect($atributo, $valorbuscado)->paginate(5);
        } else if($valorbuscado){
            $empleados=Empleado::BusqGeneral($valorbuscado)->paginate(5);
        } else {
            $empleados=Empleado::All()->paginate(5);
        }
        $relaciones = $request->relations; //  Aqui se accede al objeto relations que viene por parámetros
        //  En este bloque If se verifica que exista un objeto relaciones, en caso de que exista se accede a la primera posición del arreglo, si es all lo que contiene entonces se devuelven todas las relaciones para el modelo en cuestión.
        if ($relaciones) {
            if ($relaciones[0] === 'all') {
                $i=0;
                $length = count($empleados);
                for ($i ; $i < $length; $i++) {
                    $empleados[$i]->empresas;
                    $empleados[$i]->usuarios;
                }
            }
            else {
                $i=0;;
                $lengthEmpleados = count($empleados);
                $lengthRelaciones = count($relaciones);
                for ($i; $i < $lengthEmpleados; $i++) {
                    for ($j=0; $j < $lengthRelaciones; $j++) {
                        $relacionesMetodo = (string)$relaciones[$j];
                        $empleados[$i]->$relacionesMetodo;
                    }
                }
            }
        }
        return response()->json($empleados);
    }

    public function nomenclators()  // Nomenclators | Método que carga los Nomencladores en el Modelo:Empleado
    {
        // Sección de Carga de Nomencladores a emplear en las vista
        $sexos= Vocabulario::findorFail(28)->terminos;  // Nomenclador: Sexos
        return response()->json($sexos);  // Se envian las variables
    }

    public function store(Request $request)  // Store | Método que Guarda el Registro creado en el Modelo:Empleado
    {
        return response()->json(Empleado::create($request->all()));
    }

    public function update(Request $request,Empleado $empleado, $id)  // Update | Método que Actualiza un Registro Específico del Modelo:Empleado
    {
        return response()->json(Empleado::findOrFail($id)->update($request->all()));
    }

    public function destroyLog(Empleado $empleado, $id)  // DestroyLog | Método que Elimina de forma Lógica un Registro Específico del Modelo:Empleado
    {
        return response()->json(Empleado::findOrFail($id)->delete());
    }

    public function destroyFis(Empleado $empleado, $id)  // DestroyFis | Método que Elimina de forma Física un Registro Específico del Modelo:Empleado
    {
        return response()->json(Empleado::findOrFail($id)->forceDelete());
    }

    public function restoreLog(Empleado $empleado, $id)  // RestoreLog | Método que Restaura un Registro Específico, eliminado de forma Lógica del Modelo:Empleado
    {
        return response()->json(Empleado::findOrFail($id)->restore());
    }
}

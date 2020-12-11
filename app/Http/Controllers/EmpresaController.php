<?php

namespace App\Http\Controllers;

use App\Empresa;
use App\Vocabulario;
use Illuminate\Http\Request;

class EmpresaController extends Controller
{
    public function index(Request $request)  // Index | Método que lista todos los Registros del Modelo:Empresa
    {
        $valorbuscado = $request->valorbuscado;
        $atributo = $request->atributo;
        if ( ($atributo) && ($valorbuscado) ) {
            $empresas=Empresa::BusqSelect($atributo, $valorbuscado)->paginate(5);
        } else if($valorbuscado){
            $empresas=Empresa::BusqGeneral($valorbuscado)->paginate(5);
        } else {
            $empresas=Empresa::withTrashed()->get();
        }
        $relaciones = $request->relations; //  Aqui se accede al objeto relations que viene por parámetros
        //  En este bloque If se verifica que exista un objeto relaciones, en caso de que exista se accede a la primera posición del arreglo, si es all lo que contiene entonces se devuelven todas las relaciones para el modelo en cuestión.
        if ($relaciones) {
            if ($relaciones[0] === 'all') {
                $i=0;
                $length = count($empresas);
                for ($i ; $i < $length; $i++) {
                    $empresas[$i]->proyectos;
                    $empresas[$i]->empleados;
                }
            }
            else {
                $i=0;;
                $lengthEmpresas = count($empresas);
                $lengthRelaciones = count($relaciones);
                for ($i; $i < $lengthEmpresas; $i++) {
                    for ($j=0; $j < $lengthRelaciones; $j++) {
                        $relacionesMetodo = (string)$relaciones[$j];
                        $empresas[$i]->$relacionesMetodo;
                    }
                }
            }
        }
        return response()->json($empresas);
    }

    public function nomenclators()  // Nomenclators | Método que carga los Nomencladores en el Modelo:Empresa
    {
        // Sección de Carga de Nomencladores a emplear en las vista
        $tipsEntid= Vocabulario::findorFail(35)->terminos;  // Nomenclador: Tipos de Entidades
        $persJurid= Vocabulario::findorFail(24)->terminos;  // Nomenclador: Personalidad Jurídica
        return response()->json($tipsEntid,$persJurid);  // Se envian las variables
    }

    public function store(Request $request)  // Store | Método que Guarda el Registro creado en el Modelo:Empresa
    {
        return response()->json(Empresa::create($request->all()));
    }

    public function update(Request $request,Empresa $empresa, $id)  // Update | Método que Actualiza un Registro Específico del Modelo:Empresa
    {
        return response()->json(Empresa::findOrFail($id)->update($request->all()));
    }

    public function destroyLog(Empresa $empresa, $id)  // DestroyLog | Método que Elimina de forma Lógica un Registro Específico del Modelo:Empresa
    {
        return response()->json(Empresa::findOrFail($id)->delete());
    }

    public function destroyFis(Empresa $empresa, $id)  // DestroyFis | Método que Elimina de forma Física un Registro Específico del Modelo:Empresa
    {
        return response()->json(Empresa::findOrFail($id)->forceDelete());
    }

    public function restoreLog(Empresa $empresa, $id)  // RestoreLog | Método que Restaura un Registro Específico, eliminado de forma Lógica del Modelo:Empresa
    {
        return response()->json(Empresa::findOrFail($id)->restore());
    }
}
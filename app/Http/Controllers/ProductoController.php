<?php

namespace App\Http\Controllers;

use App\Producto;
use App\Vocabulario;
use Illuminate\Http\Request;


class ProductoController extends Controller
{
    public function index(Request $request)  // Index | Método que lista todos los Registros del Modelo:Producto
    {
        $valorbuscado = $request->valorbuscado;
        $atributo = $request->atributo;
        if ( ($atributo) && ($valorbuscado) ) {
            $productos=Producto::withTrashed()->BusqSelect($atributo, $valorbuscado)->get();
        } else if($valorbuscado){
            $productos=Producto::withTrashed()->BusqGeneral($valorbuscado)->get();
        } else {
            $productos=Producto::withTrashed()->get();
        }
        $relaciones = $request->relations; //  Aqui se accede al objeto relations que viene por parámetros
        //  En este bloque If se verifica que exista un objeto relaciones, en caso de que exista se accede a la primera posición del arreglo, si es all lo que contiene entonces se devuelven todas las relaciones para el modelo en cuestión.
        if ($relaciones) {
            if ($relaciones[0] === 'all') {
                $i=0;
                $length = count($productos);
                for ($i ; $i < $length; $i++) {
                    $productos[$i]->proyecto;
                    $productos[$i]->elementos;
                }
            }
            else {
                $i=0;;
                $lengthProductos = count($productos);
                $lengthRelaciones = count($relaciones);
                for ($i; $i < $lengthProductos; $i++) {
                    for ($j=0; $j < $lengthRelaciones; $j++) {
                        $relacionesMetodo = (string)$relaciones[$j];
                        $productos[$i]->$relacionesMetodo;
                    }
                }
            }
        }
        return response()->json($productos);
    }

    public function nomenclators()  // Nomenclators | Método que carga los Nomencladores en el Modelo:Producto
    {
        // Sección de Carga de Nomencladores a emplear en las vista
        $anos= Vocabulario::findorFail(2)->terminos;  // Nomenclador: Años
        $selloDisc= Vocabulario::findorFail(27)->terminos;  // Nomenclador: Sellos Discográficos
        $estdDigit= Vocabulario::findorFail(9)->terminos;  // Nomenclador: Estados Digitalización
        $statComer= Vocabulario::findorFail(30)->terminos;  // Nomenclador: Status Comerciales del Producto
        $destComerc= Vocabulario::findorFail(6)->terminos;  // Nomenclador: Destinos Comerciales
        $genMusic= Vocabulario::findorFail(16)->terminos;  // Nomenclador: Géneros Musicales
        $rolesInterp= Vocabulario::findorFail(26)->terminos;  // Nomenclador: Roles de Intérpretes
        return response()->json([[$anos],[$genMusic],[$estdDigit],[$statComer],[$destComerc],[$selloDisc],[$rolesInterp]]);  // Se envian las variables
    }

    public function store(Request $request)  // Store | Método que Guarda el Registro creado en el Modelo:Producto
    {
        $producto = Producto::create($request->all());
        if ($request->identificadorProd !== null) {
            $producto->setIdentificadorProdAttribute($request->identificadorProd);
        } else
            $producto->setIdentificadorProdAttributeDefault();
        $producto->save();
        return response()->json($producto);
    }

    public function update(Request $request)  // Update | Método que Actualiza un Registro Específico del Modelo:Producto
    {
        $producto = Producto::findOrFail($request->id);
        if ($request->identificadorProd !== null) {
            $producto->setIdentificadorProdAttribute($request->identificadorProd);
        } else if ($request->img_default) {
            $producto->setIdentificadorProdAttributeDefault();
        }
        return response()->json($producto->update($request->all()));
    }

    public function destroyLog($id)  // DestroyLog | Método que Elimina de forma Lógica un Registro Específico del Modelo:Producto
    {
        return response()->json(Producto::findOrFail($id)->delete());
    }

    public function destroyFis($id)  // DestroyFis | Método que Elimina de forma Física un Registro Específico del Modelo:Producto
    {
        return response()->json(Producto::withTrashed()->findOrFail($id)->forceDelete());
    }

    public function restoreLog($id)  // RestoreLog | Método que Restaura un Registro Específico, eliminado de forma Lógica del Modelo:Producto
    {
        return response()->json(Producto::onlyTrashed()->findOrFail($id)->restore());
    }
}

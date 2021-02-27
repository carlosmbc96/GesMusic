<?php

namespace App\Http\Controllers;

use App\Producto;
use App\Producto_Audiovisual;
use App\Producto_Fonograma;
use App\Proyecto;
use App\Vocabulario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductoController extends Controller
{
    public function index(Request $request)  // Index | Método que lista todos los Registros del Modelo:Producto
    {
        $valorbuscado = $request->valorbuscado;
        $atributo = $request->atributo;
        if (($atributo) && ($valorbuscado)) {
            $productos = Producto::withTrashed()->BusqSelect($atributo, $valorbuscado)->get();
        } else if ($valorbuscado) {
            $productos = Producto::withTrashed()->BusqGeneral($valorbuscado)->get();
        } else {
            $productos = Producto::withTrashed()->get();
        }
        $relaciones = $request->relations; //  Aqui se accede al objeto relations que viene por parámetros
        //  En este bloque If se verifica que exista un objeto relaciones, en caso de que exista se accede a la primera posición del arreglo, si es all lo que contiene entonces se devuelven todas las relaciones para el modelo en cuestión.
        if ($relaciones) {
            if ($relaciones[0] === 'all') {
                $i = 0;
                $length = count($productos);
                for ($i; $i < $length; $i++) {
                    $productos[$i]->proyecto;
                    $productos[$i]->fonogramas;
                    $productos[$i]->audiovisuales;
                    $productos[$i]->elementos;
                }
            } else {
                $i = 0;;
                $lengthProductos = count($productos);
                $lengthRelaciones = count($relaciones);
                for ($i; $i < $lengthProductos; $i++) {
                    for ($j = 0; $j < $lengthRelaciones; $j++) {
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
        $anos = Vocabulario::findorFail(2)->terminos;  // Nomenclador: Años
        $selloDisc = Vocabulario::findorFail(27)->terminos;  // Nomenclador: Sellos Discográficos
        $estdDigit = Vocabulario::findorFail(9)->terminos;  // Nomenclador: Estados Digitalización
        $statComer = Vocabulario::findorFail(30)->terminos;  // Nomenclador: Status Comerciales del Producto
        $destComerc = Vocabulario::findorFail(6)->terminos;  // Nomenclador: Destinos Comerciales
        $genMusic = Vocabulario::findorFail(16)->terminos;  // Nomenclador: Géneros Musicales
        $rolesInterp = Vocabulario::findorFail(26)->terminos;  // Nomenclador: Roles de Intérpretes
        return response()->json([[$anos], [$genMusic], [$estdDigit], [$statComer], [$destComerc], [$selloDisc], [$rolesInterp]]);  // Se envian las variables
    }

    public function store(Request $request)  // Store | Método que Guarda el Registro creado en el Modelo:Producto
    {
        $producto = Producto::create([
            "codigProd" => $request->codigProd,
            "añoProd" => $request->añoProd,
            "tituloProd" => $request->tituloProd,
            "descripEspPro" => $request->descripEspPro,
            "descripIngPro" => $request->descripIngPro,
            "proyecto_id" => $request->proyecto_id,
            "portadillaProd" => $request->portadillaProd,
            "sellodiscProd" => $request->sellodiscProd,
            "estadodigProd" => $request->estadodigProd,
            "statusComProd" => $request->statusComProd,
            "destinosComerPro" => $request->destinosComerPro,
            "producPrincProd" => $request->producPrincProd,
            "codigBarProd" => $request->codigBarProd,
            "genMusicPro" => $request->genMusicPro,
            "activoCatalbisPro" => $request->activoCatalbisPro,
            "catalDigitalPro" => $request->catalDigitalPro,
            "primeraPantProd" => $request->primeraPantProd,
            "variosInterpretesProd" => $request->variosInterpretesProd,
            "interpretesProd" => $request->interpretesProd,
            "autoresProd" => $request->autoresProd,
            "dirArbolProd" => $request->dirArbolProd
        ]);
        $codigProy = Proyecto::findOrFail($request->proyecto_id)->codigProy;
        Storage::disk('local')->makeDirectory('/Proyectos/' . $codigProy . "/" . $request->codigProd);
        if ($request->identificadorProd !== null) {
            $producto->setIdentificadorProdAttribute($request->identificadorProd, $request->codigProd);
        } else
            $producto->setIdentificadorProdAttributeDefault();
        $producto->save();
        return response()->json($producto);
    }

    public function update(Request $request)  // Update | Método que Actualiza un Registro Específico del Modelo:Producto
    {
        $producto = Producto::findOrFail($request->id);
        if ($request->identificadorProd !== null) {
            if (substr($producto->identificadorProd, 29) !== "Logo ver vertical_Ltr Negras.png") {
                Storage::disk('local')->delete('/Imagenes/Productos/' . substr($producto->identificadorProd, 29));
            }
            $producto->setIdentificadorProdAttribute($request->identificadorProd, $request->codigProd);
        } else if ($request->img_default) {
            Storage::disk('local')->delete('/Imagenes/Productos/' . substr($producto->identificadorProd, 29));
            $producto->setIdentificadorProdAttributeDefault();
        }
        if ($request->proyecto_id !== $producto->proyecto_id) {
            $directorios = Storage::disk('local')->allDirectories('/Proyectos/' . $producto->proyecto()->withTrashed()->get()[0]->codigProy . "/" . $producto->codigProd);
            Storage::disk('local')->deleteDirectory('/Proyectos/' . $producto->proyecto()->withTrashed()->get()[0]->codigProy . "/" . $producto->codigProd);
            $codigProy = Proyecto::findOrFail($request->proyecto_id)->codigProy;
            $nuevo_directorio = "";
            $directorio = "";
            for ($i = 0; $i < count($directorios); $i++) {
                $directorio = substr($directorios[$i], 20);
                $nuevo_directorio = $codigProy . "/" . $directorio;
                Storage::disk('local')->makeDirectory("/Proyectos/" . $nuevo_directorio);
            }
        }
        $producto->update([
            "codigProd" => $request->codigProd,
            "añoProd" => $request->añoProd,
            "tituloProd" => $request->tituloProd,
            "descripEspPro" => $request->descripEspPro,
            "descripIngPro" => $request->descripIngPro,
            "proyecto_id" => $request->proyecto_id,
            "portadillaProd" => $request->portadillaProd,
            "sellodiscProd" => $request->sellodiscProd,
            "estadodigProd" => $request->estadodigProd,
            "statusComProd" => $request->statusComProd,
            "destinosComerPro" => $request->destinosComerPro,
            "producPrincProd" => $request->producPrincProd,
            "codigBarProd" => $request->codigBarProd,
            "genMusicPro" => $request->genMusicPro,
            "activoCatalbisPro" => $request->activoCatalbisPro,
            "catalDigitalPro" => $request->catalDigitalPro,
            "primeraPantProd" => $request->primeraPantProd,
            "variosInterpretesProd" => $request->variosInterpretesProd,
            "interpretesProd" => $request->interpretesProd,
            "autoresProd" => $request->autoresProd,
            "dirArbolProd" => $request->dirArbolProd
        ]);
        return response()->json($producto);
    }

    public function destroyLog($id)  // DestroyLog | Método que Elimina de forma Lógica un Registro Específico del Modelo:Producto
    {
        return response()->json(Producto::findOrFail($id)->delete());
    }

    public function destroyFis($id)  // DestroyFis | Método que Elimina de forma Física un Registro Específico del Modelo:Producto
    {
        $producto = Producto::withTrashed()->findOrFail($id);
        for ($i = count($producto->audiovisuales()->withTrashed()->get()) - 1; $i >= 0; $i--) {
            $producto->audiovisuales()->withTrashed()->get()[$i]->pivot->delete();
        }
        for ($i = count($producto->fonogramas()->withTrashed()->get()) - 1; $i >= 0; $i--) {
            $producto->fonogramas()->withTrashed()->get()[$i]->pivot->delete();
        }
        Storage::disk('local')->deleteDirectory('/Proyectos/' . $producto->proyecto()->withTrashed()->get()[0]->codigProy . "/" . $producto->codigProd);
        if (substr($producto->identificadorProd, 29) !== "Logo ver vertical_Ltr Negras.png") {
            Storage::disk('local')->delete('/Imagenes/Productos/' . substr($producto->identificadorProd, 29));
        }
        return response()->json($producto->forceDelete());
    }

    public function restoreLog($id)  // RestoreLog | Método que Restaura un Registro Específico, eliminado de forma Lógica del Modelo:Producto
    {
        return response()->json(Producto::onlyTrashed()->findOrFail($id)->restore());
    }
    public function fonogramasRelation($id)  // RestoreLog | Método que Restaura un Registro Específico, eliminado de forma Lógica del Modelo:Producto
    {
        return response()->json(Producto::findOrFail($id)->fonogramas);
    }
    public function eliminarRelacionAud(Request $request)
    {
        $producto = Producto::withTrashed()->findOrFail($request->idProd);
        for ($i = 0; $i < count($producto->audiovisuales()->withTrashed()->get()); $i++) {
            if ($producto->audiovisuales()->withTrashed()->get()[$i]->pivot->audiovisual_id === $request->idAud) {
                $producto->audiovisuales()->withTrashed()->get()[$i]->pivot->delete();
            }
        }
    }
    public function actualizarRelacionesFong(Request $request)
    {
        $producto = Producto::withTrashed()->findOrFail($request->id);
        for ($i = count($producto->fonogramas()->withTrashed()->get()) - 1; $i >= 0; $i--) {
            $producto->fonogramas()->withTrashed()->get()[$i]->pivot->delete();
        }
        foreach ($request->fonogramas as $fonograma) {
            Producto_Fonograma::create([
                "producto_id" => $request->id,
                "fonograma_id" => $fonograma
            ]);
        }
        return response()->json($producto);
    }
    public function autores(Request $request)
    {
        $producto = Producto::withTrashed()->findOrFail($request->id);
        $audiovisuales = $producto->audiovisuales()->withTrashed()->get();
        $autores = [];
        $autores_id = [];
        for ($i = 0; $i < count($audiovisuales); $i++) {
            for ($j = 0; $j < count($audiovisuales[$i]->autores()->withTrashed()->get()); $j++) {
                if (!in_array($audiovisuales[$i]->autores()->withTrashed()->get()[$j]->id, $autores_id)) {
                    array_push($autores_id, $audiovisuales[$i]->autores()->withTrashed()->get()[$j]->id);
                    array_push($autores, $audiovisuales[$i]->autores()->withTrashed()->get()[$j]);
                }
            }
        }
        return response()->json($autores);
    }
    public function interpretes(Request $request)
    {
        $producto = Producto::withTrashed()->findOrFail($request->id);
        $audiovisuales = $producto->audiovisuales()->withTrashed()->get();
        $interpretes = [];
        $interpretes_id = [];
        for ($i = 0; $i < count($audiovisuales); $i++) {
            for ($j = 0; $j < count($audiovisuales[$i]->interpretes()->withTrashed()->get()); $j++) {
                if (!in_array($audiovisuales[$i]->interpretes()->withTrashed()->get()[$j]->id, $interpretes_id)) {
                    array_push($interpretes_id, $audiovisuales[$i]->interpretes()->withTrashed()->get()[$j]->id);
                    array_push($interpretes, $audiovisuales[$i]->interpretes()->withTrashed()->get()[$j]);
                }
            }
        }
        return response()->json($interpretes);
    }
}

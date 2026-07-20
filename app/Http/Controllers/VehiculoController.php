<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Colores;
use App\Models\Marcas;
use App\Models\Modelos;
use App\Models\Motores;
use Illuminate\Http\Request;
use App\Models\Vehiculos;
use App\Rules\TipoCategoriaRule;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

use function Pest\Laravel\json;

class VehiculoController extends Controller
{
    public function GetDatos(Request $request){
        $search=$request->search??'';
        $filtro=$request->filtro??'';
        if(!in_array($filtro,['economico','placas','id'])){
           return response()->json(['message'=>'Filtro no valido'],400);
        }
        $vehiculo=Vehiculos::where($filtro,'LIKE',$search)->with(['modelo.marca', 'modelo.motor'])->first();
        
    return response()->json(['datos'=>$vehiculo]);
    }
    public function GetImage(Request $request) {
        $request->validate([
            'id'=>['required','exists:vehiculos,id']
        ]);
        $vehiculo=Vehiculos::find($request->id);
        $realtype=$vehiculo->tipo_id-7;
        $path='VehiculosRecepcionVehicular/Vehiculo'.$realtype.'.png';
        $file = Storage::disk('local')->path($path);
        if (!file_exists($file)) {
            return response()->json(['message'=>'No existe la Imagen Del Tipo '.$realtype],404);
        }
        return Response::file($file, [
            'Content-Type' => mime_content_type($file)
        ]);
  }
    public function FindDatos(Request $request){
        $request->validate([
            'id'=>['required','exists:vehiculos,id']
        ]);
        $vehiculo=Vehiculos::with(['modelo.marca', 'modelo.motor', 'color'])->find($request->id);
        return response()->json(['vehiculo'=>$vehiculo]);
    }
    public function CreateOrUpdate(Request $request){
        $request->validate([
            'id'=>['nullable','exists:vehiculos,id'],
            'placas'=>['required','string','unique:vehiculos,placas,'.$request->id],
            'economico'=>['required','string','unique:vehiculos,economico,'.$request->id],
            'vin'=>['required','string','unique:vehiculos,vin,'.$request->id],
            'año'=>['required','integer','min:1899','max:'.(date('Y')+1)],
            'tipo_id'=>['required', new TipoCategoriaRule(3)],
            'color'=>['required','string'],
            'marca_id'=>['required','exists:marcas,id'],
            'motor_id'=>['required','exists:motores,id'],
            'modelo'=>['required','string','max:100'],
        ]);
        $color=Colores::firstOrCreate([
            'descripcion'=>$this->normalizeDescription($request->color)
        ]);
        $modelo=Modelos::withTrashed()->firstOrCreate([
            'descripcion'=>$this->normalizeDescription($request->modelo),
            'marca_id'=>$request->marca_id,
            'motor_id'=>$request->motor_id,
        ]);
        if ($modelo->trashed()) {
            $modelo->restore();
        }
        $vehiculo = Vehiculos::updateOrCreate(
            ['id' => $request->id], // condición de búsqueda

            [   // valores a actualizar o crear
                'placas'    => $request->placas,
                'año'       => $request->año,
                'economico' => $request->economico,
                'vin'       => $request->vin,
                'tipo_id'   => $request->tipo_id,
                'color_id'  => $color->id,
                'modelo_id' => $modelo->id,
            ]
        );
        $vehiculo->load(['modelo.marca', 'modelo.motor']);
        return response()->json(['vehiculo'=>$vehiculo,'message'=>($request->id? 'Actualizado' : 'Creado').' Correctamente']);
    }

    public function CreateCatalog(Request $request)
    {
        $validated=$request->validate([
            'tipo'=>['required',Rule::in(['marca','motor'])],
            'descripcion'=>['required','string','max:100'],
        ]);
        $descripcion=$this->normalizeDescription($validated['descripcion']);

        if ($validated['tipo'] === 'marca') {
            $catalogo=Marcas::withTrashed()->firstOrCreate(compact('descripcion'));
        } elseif ($validated['tipo'] === 'motor') {
            $catalogo=Motores::withTrashed()->firstOrCreate(compact('descripcion'));
        }
        if ($catalogo->trashed()) {
            $catalogo->restore();
        }

        return response()->json([
            'option'=>[
                'value'=>$catalogo->id,
                'label'=>$catalogo->descripcion,
            ],
        ],201);
    }

    private function normalizeDescription(string $description): string
    {
        return mb_strtolower(trim($description), 'UTF-8');
    }
}

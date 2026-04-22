<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Colores;
use App\Models\Marcas;
use App\Models\Modelos;
use Illuminate\Http\Request;
use App\Models\Vehiculos;
use App\Rules\TipoCategoriaRule;
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
        $vehiculo=Vehiculos::where($filtro,'LIKE',$search)->with('modelo.marca')->first();
        
    return response()->json(['datos'=>$vehiculo]);
    }
    public function FindDatos(Request $request){
        $request->validate([
            'id'=>['required','exists:vehiculos,id']
        ]);
        $vehiculo=Vehiculos::with(['modelo.marca','color'])->find($request->id);
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
            'modelo'=>['required','string'],
            'marca'=>['required','string'],
        ]);
        $color=Colores::firstOrCreate([
            'descripcion'=>strtolower($request->color)
        ]);
        $marca=Marcas::firstOrCreate([
            'descripcion'=>strtolower($request->marca)
        ]);
        $modelo=Modelos::firstOrCreate([
            'descripcion'=>strtolower($request->modelo),
            'marca_id'=>$marca->id
        ]);

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
        $vehiculo->load('modelo.marca');
        return response()->json(['vehiculo'=>$vehiculo,'message'=>($request->id? 'Actualizado' : 'Creado').' Correctamente']);
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Vehiculos;

use function Pest\Laravel\json;

class VehiculoController extends Controller
{
    public function GetDatos(Request $request){
        $search=$request->search??'';
        $filtro=$request->filtro??'';
        if(!in_array($filtro,['economico','placas'])){
           return response()->json(['message'=>'Filtro no valido'],400);
        }
        $vehiculo=Vehiculos::where($filtro,'LIKE',$search)->with('modelo.marca')->first();
        
    return response()->json(['datos'=>$vehiculo]);
    }
}

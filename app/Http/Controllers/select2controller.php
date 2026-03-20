<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\RegimenesFiscales;
use App\Models\VehiculosConceptos;
use App\Models\VehiculosConceptosDisponibles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class select2controller extends Controller
{
    public function Empresas(Request $request){
        $options=collect([['value'=>1,'label'=>'es prueba'],['value'=>2,'label'=>'es prueba muy muy larga']]);
        return response()->json(compact('options'));
    }
    public function RegimenesFiscales(Request $request){
        $options=RegimenesFiscales::query();
        if($request->filled('query')){
            $search='%'.($request->input('query')).'%';
            $options=$options->where('descripcion','LIKE',$search)->orWhere('clave','LIKE' ,$search);
        }
        $options=$options->take(20)->get()->map(fn($item)=> [
            'value'=>$item->clave,
            'label'=>$item->clave.'-'.$item->descripcion
        ]);

        return response()->json(compact('options'));
    }
    public function VehiculosConceptosPorModulo(Request $request){

        $validation=Validator::make($request->all(),['id_modulo'=>['required','exists:modulo_ordenes_servicios,id'],'query'=>['string']]);
        $options=[];
        if (!$validation->fails()){
            $search="%".$validation->validated()['query']."%";
            $options=VehiculosConceptos::join('vehiculos_conceptos_disponibles','vehiculos_conceptos_disponibles.vehiculo_concepto_id','=','vehiculos_conceptos.id')
            ->where('vehiculos_conceptos_disponibles.modulo_orden_id',$validation->validated()['id_modulo'])
            ->where('descripcion','LIKE',$search)
            ->get()->map(fn($item)=> [
            'value'=>$item->id,
            'label'=>$item->descripcion
            ]);
        }
        return response()->json(compact('options'));
    }
}

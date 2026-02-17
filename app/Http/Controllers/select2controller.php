<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\RegimenesFiscales;
use Illuminate\Http\Request;

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
}

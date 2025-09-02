<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class select2controller extends Controller
{
    public function Empresas(Request $request){
        $options=collect([['value'=>1,'label'=>'es prueba'],['value'=>2,'label'=>'es prueba muy muy larga']]);
        return response()->json(compact('options'));
    }
}

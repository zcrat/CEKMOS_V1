<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\OrdenesServicio;
class ComboboxController extends Controller
{
    public function GetOrdenesServicio(Request $request){
        $search=$request->search??'';
        $user=$request->user()->load('modulos_orden');

        $ordenes=OrdenesServicio::where('orden_servicio','LIKE','%'.$search.'%')->whereIn('modulo_orden_id',$user->modulos_orden->pluck('modulo_orden_id'))->pluck('orden_servicio');
        $ordenes=$search==''?[]:['dffd','dsdsd','dsdsdedevdf'];
        return response()->json(['options'=>$ordenes]);

    }
}

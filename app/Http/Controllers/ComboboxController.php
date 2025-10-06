<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\OrdenesServicio;
use App\Models\usuariosTaller;
use App\Models\Vehiculos;

class ComboboxController extends Controller
{
    public function GetOrdenesServicio(Request $request){
        $search=$request->search??'';
        $user=$request->user()->load('modulos_orden');
        $ordenes=OrdenesServicio::where('orden_servicio','LIKE','%'.$search.'%')->whereIn('modulo_orden_id',$user->modulos_orden->pluck('modulo_orden_id'))->pluck('orden_servicio');
        return response()->json(['options'=>$ordenes]);
    }
    public function GetUbicaciones(Request $request){
        $search=$request->search??'';
        $ubicaciones=OrdenesServicio::where('ubicacion','LIKE','%'.$search.'%')->pluck('ubicacion')->unique()->values();
        return response()->json(['options'=>$ubicaciones]);
    }
    public function GetAdministradoresTrasporte(Request $request){
        $search=$request->search??'';
        $administradores=usuariosTaller::where('nombre','LIKE','%'.$search.'%')->where('tipo_id',1)->pluck('nombre')->unique()->values();
        return response()->json(['options'=>$administradores]);
    }
    public function GetJefesProceso(Request $request){
        $search=$request->search??'';
        $jefes=usuariosTaller::where('nombre','LIKE','%'.$search.'%')->where('tipo_id',2)->pluck('nombre')->unique()->values();
        return response()->json(['options'=>$jefes]);
    }
    public function GetTrabajadores(Request $request){
        $search=$request->search??'';
        $trabajadores=usuariosTaller::where('nombre','LIKE','%'.$search.'%')->where('tipo_id',3)->pluck('nombre')->unique()->values();
        return response()->json(['options'=>$trabajadores]);
    }
    public function GetTecnicos(Request $request){
        $search=$request->search??'';
        $tecnicos=usuariosTaller::where('nombre','LIKE','%'.$search.'%')->where('tipo_id',4)->pluck('nombre')->unique()->values();
        return response()->json(['options'=>$tecnicos]);
    }
    public function GetVehiculoEconomico(Request $request){
        $search=$request->search??'';
        $economicos=Vehiculos::where('economico','LIKE','%'.$search.'%')->pluck('economico')->unique()->values();
        return response()->json(['options'=>$economicos]);
    }
    public function GetVehiculoPlacas(Request $request){
        $search=$request->search??'';
        $placas=Vehiculos::where('placas','LIKE','%'.$search.'%')->pluck('placas')->unique()->values();
        return response()->json(['options'=>$placas]);
    }
}

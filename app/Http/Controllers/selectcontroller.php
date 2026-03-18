<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Estatus;
use App\Models\NivelesCombustible;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ModuloOrdenesServicio;
use App\Models\Tipos;

class selectcontroller extends Controller
{
    public function TiposVehiculosGeneral(Request $request){
        $options=Tipos::selectraw("id as value , descripcion as label")->where('categoria_id',3)->get();
        return response()->json(compact('options'));
    }
    public function NivelesCombustible(Request $request){
        $options=NivelesCombustible::selectraw("id as value , descripcion as label")->get();
        return response()->json(compact('options'));
    }
    public function ModulosOrden(Request $request){
        $user=Auth::user()->load('modulos_orden');
        $options=ModuloOrdenesServicio::query();
        if(!$request->user()->can('ver.todo.modulos.presupuestos')){
            $modulosvisibles=$user->modulos_orden ? $user->modulos_orden->pluck('modulo_orden_id')->toarray(): [] ;
            $options->whereIn('id',$modulosvisibles);
        }
        $options=$options->selectraw("id as value , descripcion as label")->orderByDesc('año')->get();
        return response()->json(compact('options'));
    }
    public function EstatusIdsPerCategory(Request $request){
        $category=$request->input('categoria_id');
        $options=[];
        if(!empty($category)){
            $options=Estatus::where('categoria_id',$category)->selectraw("id as value , descripcion as label")->get();
        }
        return response()->json(compact('options'));
    }
}

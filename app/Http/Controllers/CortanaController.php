<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Modulos;
use App\Models\Presupuestos;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
class CortanaController extends Controller
{
    public function PresupuestosVista(Request $request){
        return Inertia::render('Cortana/Presupuestos');
    }
    public function GetItems(Request $request){
        $user=Auth::user()->load('modulos_orden');
        $currentPage=$request->currentPage ?? 1;
        $itemsPerPage=$request->itemsPerPage ?? 10;
        $offset=($currentPage-1)*$itemsPerPage;
        $modulosvisibles=$user->modulos_orden ? $user->modulos_orden->pluck('modulo_orden_id')->toarray(): [] ;
        $query=Presupuestos::whereHas('orden_servicio', function ($query) use($modulosvisibles){
            $query->whereIn('modulo_orden_id', $modulosvisibles);
        });
        //aplicar filtros

        $totalItems=(clone $query)->count();
        $items=$query->skip($offset)->take($itemsPerPage);

        $totalPages=ceil($totalItems/$itemsPerPage);
        return response()->json(
            compact('currentPage', 'itemsPerPage', 'totalPages', 'totalItems', 'items')
        );
    }
}

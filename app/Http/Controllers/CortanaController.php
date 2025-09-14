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
       $user=Auth::user()->load('modulos_orden');

    $modulosvisibles=$user->modulos_orden ? $user->modulos_orden->pluck('modulo_orden_id')->toarray(): [] ;
    $presupuestos=Presupuestos::whereHas('orden_servicio', function ($query) use($modulosvisibles){
        $query->whereIn('modulo_orden_id', $modulosvisibles);
    })->take(10);
    $totalitems=Presupuestos::whereHas('orden_servicio', function ($query) use($modulosvisibles){
        $query->whereIn('modulo_orden_id', $modulosvisibles);
    })->count();


        return Inertia::render('Cortana/Presupuestos', [
            'currentpage' => 1,
            'perpage' => 10,
            'totalpages' => ceil($totalitems/10),
            'totalitems' => $totalitems,
            'items' => $presupuestos,
        ]);
        return redirect()->route('dashboard');
    }
}

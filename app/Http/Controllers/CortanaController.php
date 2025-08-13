<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Modulos;
use Inertia\Inertia;
class CortanaController extends Controller
{
    public function PresupuestosVista(Request $request){
        if ($request->keys() === ['modulo']) {
           if(Modulos::where('nombre',$request->modulo)->exists()){
              return Inertia::render('Cortana/Presupuestos', [
                'modulo' => $request->input('modulo'),
            ]);
           }
        }else if($request->filled('modulo')){
            return redirect()->route('Cortana.Presupuesto.Vista',['modulo'=>$request->modulo]);
        };
        return redirect()->route('dashboard');
    }
}

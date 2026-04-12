<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Modulos;
use App\Models\Presupuestos;
use App\Models\Vehiculos;
use App\Rules\ExistTipo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
class CortanaController extends Controller
{
    public function PresupuestosVista(Request $request){
        return Inertia::render('Cortana/Presupuestos');
    }
    public function RecepcionVehicularVista(Request $request){
        return Inertia::render('Cortana/RecepcionVehicular');
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
    public function CreateOrdenServico(Request $request){
        $user=Auth::user()->load('modulos_orden');
        $validator=Validator::make($request->all(),[
            'orden_servicio'=>['nullable','string','max:20'],
            'folio'=>['nullable','string','max:20'],
            'orden_seguimiento'=>['nullable','string','max:20'],
            'ubicacion'=>['required','string','max:100'],
            'telefono'=>['required','string','max:20'],
            'empresa_id'=>['required','exists:empresas,id'],
            'cliente_id'=>['required','exists:clientes,id'],
            'gasolina'=>['required','exists:niveles_combustible,id'],
            'kilometraje'=>['required','integer','min:0'],
            'estimacion'=>['required','date'],
            'administrador'=>['required','string','max:100'],
            'jefe'=>['required','string','max:100'],
            'trabajador'=>['required','string','max:100'],
            'tecnico'=>['required','string','max:100'],
            'descripcion_mo'=>['required','string'],
            'indicaciones_cliente'=>['required','string'],
            'garantia'=>['required','string'],
            'observaciones'=>['required','string'],
            'tipo_id'=>['required',new ExistTipo(2, $request->tipo_id)],
            'vehiculo_concepto_id'=>['required','exists:vehiculos_conceptos,id'],
            'economico'=>['required','string','max:20'],
            'placas'=>['required','string','max:20'],
            'vin'=>['required','string','max:50'],
            'marca'=>['required','string','max:50'],
            'modelo'=>['required','string','max:50'],
            'año'=>['required','integer','min:1900','max:2100'],
            // 'vigencia'=>['nullable','date'],
            'modulo_orden'=>['required','integer','exists:modulo_ordenes_servicios,id'],
        ]);
       
        $validator->after(function ($validator) use ($request, $user) {
            $modulosPermitidos = $user->modulos_orden->pluck('modulo_orden_id')->toArray();
            if ($request->filled('modulo_orden') && !in_array($request->modulo_orden, $modulosPermitidos)) {
                $validator->errors()->add('modulo_orden', 'El usuario no tiene permiso para este módulo de orden.');
            }
            if(!Vehiculos::where('economico',$request->economico)->orWhere('placas',$request->placas)->exists()){
                if(Vehiculos::where('economico',$request->economico)->exists()){
                    $validator->errors()->add('economico', 'el economico registrado con otras placas');
                }
                if(Vehiculos::where('placas',$request->placas)->exists()){
                    $validator->errors()->add('placas', 'las placas registradas en otro economico'); 
                }
            }
        });

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        } 
    }
}

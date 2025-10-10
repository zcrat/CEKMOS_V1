<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\OrdenesServicio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class PresupuestosController extends Controller
{
    public function GetDataPerOrdenServicio(Request $request)
    {
        $user=Auth::user()->load('modulos_orden');
        if($request->filled('orden_servicio')){
            $orden=$request->orden_servicio;
            $data=OrdenesServicio::where('orden_servicio',$orden)
            ->with([
                'entrada',
                'responsables.administrador_transporte',
                'responsables.jefe_de_proceso',
                'responsables.trabajador',
                'responsables.tecnico',
                'vehiculo.modelo.marca',
                'vehiculo_concepto',
                'empresa',
                'cliente',
            ])->first();
            if($data){
                if(!in_array($data->modulo_orden_id,$user->modulos_orden->pluck('modulo_orden_id')->toarray())){
                    return response()->json(null);
                };
                $presupuesto=[
                    'orden_servicio'=> $data->orden_servicio,
                    'folio'=> '',
                    'orden_seguimiento'=> '',
                    'ubicacion'=> $data->ubicacion,
                    'telefono'=> $data->telefono,
                    'empresa_id'=> $data->empresa_id,
                    'cliente_id'=>$data->cliente_id,
                    'gasolina'=> $data->entrada->gasolina,
                    'kilometraje'=> $data->entrada->kilomentraje,
                    'estimacion'=> $data->entrada->estimacion->format('Y-m-d\TH:i'),
                    'administrador'=>$data->responsables->administrador_transporte->nombre,
                    'jefe'=>$data->responsables->jefe_de_proceso->nombre,
                    'trabajador'=>$data->responsables->trabajador->nombre,
                    'tecnico'=>$data->responsables->tecnico->nombre,
                    'descripcion_mo'=> $data->notas_mecanico,
                    'indicaciones_cliente'=> $data->indicaciones_cliente,
                    'garantia'=>'LO ESTIPULADO EN EL CONTRATO',
                    'observaciones'=>'DE ACUERDO A LO DIFICIL DE LA FALLA PARA SU REPARACION',
                    'tipo_id'=>3,
                    'vehiculo_concepto_id'=> $data->vehiculo_concepto_id,
                    'economico'=> $data->vehiculo->economico,
                    'placas'=> $data->vehiculo->placas,
                    'vin'=> $data->vehiculo->vin,
                    'marca'=> $data->vehiculo->modelo->marca->descripcion,
                    'modelo'=> $data->vehiculo->modelo->descripcion,
                    'año'=> $data->vehiculo->año,
                    'vigencia'=> null,
                    'modulo_orden'=>$data->modulo_orden_id,
                ];
                return response()->json([
                    'presupuesto'=>$presupuesto,
                    'empresa'=>['value'=>$data->empresa->id,'label'=>$data->empresa->nombre],
                    'cliente'=>['value'=>$data->cliente->id,'label'=>$data->cliente->nombre],
                    'vehiculo_concepto'=>['value'=>$data->vehiculo_concepto->id,'label'=>$data->vehiculo_concepto->descripcion],
                ]);
            }
        return response()->json(null);
        }
    }
    public function CreatePresupuesto(Request $request)
    {
        $user=Auth::user()->load('modulos_orden');
        $data=$request->validate([
            'orden_servicio'=>'nullable|string|max:20',
            'folio'=>'nullable|string|max:20',
            'orden_seguimiento'=>'nullable|string|max:20',
            'ubicacion'=>'required|string|max:100',
            'telefono'=>'required|string|max:20',
            'empresa_id'=>'required|exists:empresas,id',
            'cliente_id'=>'required|exists:clientes,id',
            'gasolina'=>'required||exists:niveles_combustible,id',
            'kilometraje'=>'required|integer|min:0',
            'estimacion'=>'required|date',
            'administrador'=>'required|string|max:100',
            'jefe'=>'required|string|max:100',
            'trabajador'=>'required|string|max:100',
            'tecnico'=>'required|string|max:100',
            'descripcion_mo'=>'required|string',
            'indicaciones_cliente'=>'required|string',
            'garantia'=>'required|string',
            'observaciones'=>'required|string',
            'tipo_id'=>'required|integer|in:1,2,3',
            'vehiculo_concepto_id'=>'required|exists:vehiculos_conceptos,id',
            'economico'=>'required|string|max:20',
            'placas'=>'required|string|max:20',
            'vin'=>'required|string|max:50',
            'marca'=>'required|string|max:50',
            'modelo'=>'required|string|max:50',
            'año'=>'required|integer|min:1900|max:2100',
            // 'vigencia'=>'nullable|date',
            'modulo_orden'=>'required|integer|exists:modulos_orden,id',
        ]);
    }
}

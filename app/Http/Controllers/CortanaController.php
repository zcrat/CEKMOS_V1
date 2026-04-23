<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ModuloOrdenesServicio;
use Illuminate\Http\Request;
use App\Models\Modulos;
use App\Models\OrdenesServicio;
use App\Models\Presupuestos;
use App\Models\Ubicaciones;
use App\Models\Vehiculos;
use App\Rules\ExistTipo;
use BcMath\Number;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
        $keysinventario=[
            'llanta',
            'cables',
            'estuche',
            'llave_tuerca',
            'triangulo',
            'tarjeta_circulacion',
            'cubreruedas','candado_rueda','extinguidor','gato','placas'
        ];
        $keyspintura=['decolorada','color_desigual','rayonnes','grietas','golpes','emblemas','logos','rociado','granizo','lluvia'];
        $keycondicionesinterior=[
            'puerta_izq_f',
            'puerta_izq_t',
            'puerta_der_f',
            'puerta_der_t',
            'asiento_izq_f',
            'asiento_izq_t',
            'asiento_der_f',
            'asiento_der_t',
            'consola',
            'claxon',
            'tablero',
            'quemacocos',
            'toldo',
            'elevadores',
            'luces',
            'seguros',
            'climatizador',
            'radio',
            'retrovisor',
            'tapetes',
        ];
        $keyscondicionesexterior=[
            'antena_radio',
            'estribos',
            'antena_telefono',
            'guardafangos',
            'antena_cb',
            'parabrisas',
            'alarma',
            'limpiaparabrisas',
            'luces',
            'espejos_laterales'
        ];
        $rulesExtra = [];
        foreach ($keysinventario as $key) {
            $rulesExtra["inventario.$key"] = ['required','in:0,1'];
        }
        foreach ($keyspintura as $key) {
            $rulesExtra["pintura.$key"] = ['required','in:0,1'];
        }
        foreach ($keycondicionesinterior as $key) {
            $rulesExtra["condiciones_interiores.$key"] = ['required','exists:estatus,id'];
        }
        foreach ($keyscondicionesexterior as $key) {
            $rulesExtra["condiciones_exteriores.$key"] = ['required','exists:estatus,id'];
        } 
        $validator=Validator::make($request->all(),[
            'orden_seguimiento'=>['nullable','string','max:20'],
            'orden_opcional'=>['nullable','string','max:20'],
            'ubicacion'=>['required','string','max:100'],
            'tipo_presupuesto_id'=>['required',new ExistTipo(2, $request->tipo_presupuesto_id)],
            'modulo_orden_id'=>['required','integer','exists:modulo_ordenes_servicios,id'],
            'vehiculo_concepto_id'=>['required','exists:vehiculos_conceptos,id'],
            'empresa_id'=>['required','exists:empresas,id'],
            'cliente_id'=>['required','exists:clientes,id'],
            'vehiculo_id'=>['required','exists:vehiculos,id'],
            'telefono'=>['required','integer','digits:10'],
            'estimacion'=>['required','date'],
            'kilometraje'=>['required','integer','min:0'],
            'gasolina'=>['required','exists:niveles_combustible,id'],
            'administrador'=>['required','string','max:100'],
            'jefe'=>['required','string','max:100'],
            'trabajador'=>['required','string','max:100'],
            'tecnico'=>['required','string','max:100'],
            'indicaciones_cliente'=>['required','string'],
            'descripcion_mo'=>['required','string'],
            'folio'=>['nullable','string','max:20'],
            'garantia'=>['required','string'],
            'observaciones'=>['required','string'],
            'inventario'=>['required','array'],
            'pintura'=>['required','array'],
            'condiciones_interiores'=>['required','array'],
            'condiciones_exteriores'=>['required','array'],
            'imagenes_evidencia'=>['required','array'],
            'imagenes_evidencia.*'=>['required','file','image','max:2048'],
            'carro'=>['nullable','file','image','max:2048'],
            'firma'=>['nullable','file','image','max:2048'],
            ...$rulesExtra,
        ]);
       
        $validator->after(function ($validator) use ($request, $user) {
            $modulosPermitidos = $user->modulos_orden->pluck('modulo_orden_id')->toArray();
            if ($request->filled('modulo_orden_id') && !(in_array($request->modulo_orden_id, $modulosPermitidos) || $request->user()->hasRole('Super Admin'))) {
                $validator->errors()->add('modulo_orden', 'El usuario no tiene permiso para este módulo de orden.');
            }
        });
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        try{
            DB::beginTransaction();
            $ubicacion=Ubicaciones::FirstOrCreate(['nombre'=>
                strtolower(trim($request->ubicacion))
            ]);
            $orden=$this->GetClave($request->modulo_orden_id);
            $seguimiento=$request->orden_seguimiento;
            $opcional=$request->orden_seguimiento;
            $ordenservicio=OrdenesServicio::create([
                'orden_servicio'=>$orden,
                'orden_seguimiento'=>$seguimiento ?? $orden,
                'orden_opcional'=>$opcional,
                'modulo_orden_id'=>$request->modulo_orden_id,
                'vehiculo_id'=>$request->vehiculo_id,
                'vehiculo_concepto_id'=>$request->vehiculo_concepto_id,
                'user_id'=>$request->user()->id,
                'empresa_id'=>$request->empresa_id,
                'cliente_id'=>$request->cliente_id,
                'update_fotos'=>false,
                'diagnostico'=>null,
                'indicaciones_cliente'=>$request->indicaciones_cliente,
                'notas_mecanico'=>$request->descripcion_mo,
                'notas_retraso'=>'',
                'telefono'=>$request->telefono,
                'ubicacion_id'=>$ubicacion->id,
            ]);
            $presupuesto=Presupuestos::create([
                'orden_servicio_id'=>$ordenservicio->id,
                'observaciones'=>$request->observaciones,
                'descripcion_mo'=> $request->descripcion_mo,
                'garantia'=>$request->garantia,
                'folio'=>$this->GetFolio($ordenservicio->id,$orden,$request->tipo_presupuesto_id,),
                'vigencia'=>null,
                'factura_id'=>null,
                'tipo_id'=>$request->tipo_presupuesto_id,
                'estatus_id'=>1,
            ]);


            DB::commit();
            return response()->json(['message' => 'funciona'], 400);
        }catch(\Exception $e){
            DB::rollBack();
            return response()->json(['message' => 'Error al crear la orden de servicio: '.$e->getMessage()], 500);
        }
        
    }
    public function GetClave(Number $modulo_orden_id){
        $anteriores=0;
        $clave=ModuloOrdenesServicio::find($modulo_orden_id);
        $num=OrdenesServicio::withTrashed()->where('modulo_orden_id',$modulo_orden_id)->count() + 1;
        do{
            $numeroConCeros = str_pad($num + $anteriores, 5, "0", STR_PAD_LEFT);
            $orden= ($clave->clave??'Desc').$numeroConCeros;
            $anteriores++;
        }while(OrdenesServicio::where('orden_servicio',$orden)->exist());
        return $orden;
    }
    public function GetFolio(Number $ordenservicio_id,$clave,$tipo){

        $tipos=[5=>'C',6=>'P',7=>''];
        $num=Presupuestos::withTrashed()->where('orden_servicio_id',$ordenservicio_id)->count()  + 1;
        $numeroConCeros = str_pad($num, 2, "0", STR_PAD_LEFT);
        $folio= $clave.'-'.$numeroConCeros.$tipos[$tipo];
        return $folio;
    }
}

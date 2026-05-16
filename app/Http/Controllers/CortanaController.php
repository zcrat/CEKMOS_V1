<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Archivos;
use App\Models\CondicionesPinturaOrdenServicio;
use App\Models\DatosEntrada;
use App\Models\ExterioresOrdenServicio;
use App\Models\InterioresOrdenServicio;
use App\Models\InventarioOrdenServicio;
use App\Models\ModuloOrdenesServicio;
use Illuminate\Http\Request;
use App\Models\Modulos;
use App\Models\OrdenesServicio;
use App\Models\Path;
use App\Models\Presupuestos;
use App\Models\ResponsablesOrdenServicio;
use App\Models\RutasArchivo;
use App\Models\Ubicaciones;
use App\Models\UsuariosTaller;
use App\Models\Vehiculos;
use App\Models\VehiculosConceptosDisponibles;
use App\Rules\ExistTipo;
use BcMath\Number;
use Carbon\Carbon;
use finfo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
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
    public function GetOrdenServicio(Request $request){
        $request->validate([
            'id'=>['required','exists:ordenes_servicio,id']
        ]);
        $ordenservicio=OrdenesServicio::with( [
                'interiores', 
                'exteriores', 
                'inventario', 
                'condiciones_pintura',
                'empresa',
                'cliente',
                'vehiculo',
                'vehiculo_concepto',
                'entrada',
                'responsables.administrador_transporte',
                'responsables.jefe_de_proceso',
                'responsables.trabajador',
                'responsables.tecnico',
                'archivos'
            ])->find($request->id);
        
        $responsables=$ordenservicio->responsables;
        $condicionespintura=$ordenservicio->condiciones_pintura;
        $inventariobase=$ordenservicio->inventario;
        $interioresbase=$ordenservicio->interiores;
        $exterioresbase=$ordenservicio->exteriores;

        $generales=[
            'id'=>$ordenservicio->id,
            'orden_seguimiento'=>$ordenservicio->orden_seguimiento,
            'orden_opcional'=>$ordenservicio->orden_opcional,
            'ubicacion'=>$ordenservicio->ubicacion->nombre ?? '',
            'tipo_id'=>$ordenservicio->tipo_id,
            'modulo_orden'=>$ordenservicio->modulo_orden_id,
            'vehiculo'=>[
                'value' => $ordenservicio->vehiculo_id,
                'label'=>$ordenservicio->vehiculo ? 
                ($ordenservicio->vehiculo->economico . ' - ' . $ordenservicio->vehiculo->placas): 'Desconocido', 
            ],
            'vehiculo_concepto_id'=>[
                'value' => $ordenservicio->vehiculo_concepto_id,
                'label'=>optional($ordenservicio->vehiculo_concepto)->descripcion ?? 'Desconocido',
            ],
            'empresa'=>[
                'value' => $ordenservicio->empresa_id,
                'label'=>optional($ordenservicio->empresa)->nombre ?? 'Desconocido',
            ],
            'cliente'=>[
                'value' => $ordenservicio->cliente_id,
                'label'=>optional($ordenservicio->cliente)->nombre ?? 'Desconocido',
            ],
            'estimacion'=>$ordenservicio->entrada->estimacion ?? null,
            'kilometraje'=>$ordenservicio->entrada->kilometraje,
            'gasolina'=>$ordenservicio->entrada->gasolina,
            'telefono'=>$ordenservicio->telefono,
            'administrador'=>optional($responsables->administrador_transporte)->nombre ?? null,
            'jefe'=>optional($responsables->jefe_de_proceso)->nombre ?? null,
            'trabajador'=>optional($responsables->trabajador)->nombre ?? null,
            'tecnico'=>optional($responsables->tecnico)->nombre ?? null,
            'descripcion_mo'=>$ordenservicio->notas_mecanico,
            'indicaciones_cliente'=>$ordenservicio->indicaciones_cliente,
            'cambiar_archivos'=>$ordenservicio->cambiar_archivos,
        ];


        $pintura=[
            'decolorada'=>(bool)$condicionespintura->decolorada ,
            'color_desigual'=>(bool)$condicionespintura->color_no_igual ,
            'rayones'=>(bool)$condicionespintura->exeso_rayones ,
            'grietas'=>(bool)$condicionespintura->pequenias_grietas ,
            'golpes'=>(bool)$condicionespintura->carroceria_golpes , 
            'emblemas'=>(bool)$condicionespintura->emblemas_completos , 
            'logos'=>(bool)$condicionespintura->logos ,
            'rociado'=>(bool)$condicionespintura->exeso_rociado , 
            'granizo'=>(bool)$condicionespintura->danios_granizado , 
            'lluvia'=>(bool)$condicionespintura->lluvia_acido  
        ];
        $inventario=[
            'llanta'=>(bool)$inventariobase->llanta,
            'cables'=>(bool)$inventariobase->cables_corriente,
            'estuche'=>(bool)$inventariobase->estuche_herramientas,
            'llave_tuerca'=>(bool)$inventariobase->llave_tuercas,
            'triangulo'=>(bool)$inventariobase->triangulo_seguridad,
            'tarjeta_circulacion'=>(bool)$inventariobase->tarjeta_circulacion,
            'cubreruedas'=>(bool)$inventariobase->cubreruedas,
            'candado_rueda'=>(bool)$inventariobase->candado_rueda,
            'extinguidor'=>(bool)$inventariobase->extinguidor,
            'gato'=>(bool)$inventariobase->gato,
            'placas'=>(bool)$inventariobase->placas,
        ];
        $interiores=[
            'puerta_izq_f'=> $interioresbase->puerta_interior_frontal,
            'puerta_izq_t'=> $interioresbase->puerta_interior_trasera,
            'puerta_der_f'=> $interioresbase->puerta_delantera_frontal,
            'puerta_der_t'=> $interioresbase->puerta_delantera_trasera,
            'asiento_izq_f'=> $interioresbase->asiento_interior_frontal,
            'asiento_izq_t'=> $interioresbase->asiento_interior_trasera,
            'asiento_der_f'=> $interioresbase->asiento_delantera_frontal,
            'asiento_der_t'=> $interioresbase->asiento_delantera_trasera,
            'consola'=> $interioresbase->consola_central,
            'claxon'=> $interioresbase->claxon,
            'tablero'=> $interioresbase->tablero,
            'quemacocos'=> $interioresbase->quemacocos,
            'toldo'=> $interioresbase->toldo,
            'elevadores'=> $interioresbase->elevadores_eletricos,
            'luces'=> $interioresbase->luces_interiores,
            'seguros'=> $interioresbase->seguros_eletricos,
            'climatizador'=> $interioresbase->climatizador,
            'radio'=> $interioresbase->radio,
            'retrovisor'=> $interioresbase->espejos_retrovizor,
            'tapetes'=> $interioresbase->tapetes,
        ];
        $exteriores=[
            'antena_radio'=> $exterioresbase->antena_radio,
            'estribos'=> $exterioresbase->estribos,
            'antena_telefono'=> $exterioresbase->antena_telefono,
            'guardafangos'=> $exterioresbase->guardafangos,
            'antena_cb'=> $exterioresbase->antena_cb,
            'parabrisas'=> $exterioresbase->parabrisas,
            'alarma'=> $exterioresbase->sistema_alarma,
            'limpiaparabrisas'=> $exterioresbase->limpia_parabrisas,
            'luces'=> $exterioresbase->luces_exteriores,
            'espejos_laterales'=> $exterioresbase->espejos_laterales
        ];
        $archivos=$ordenservicio->archivos;


        $pathcarro     = RutasArchivo::where('tipo_id', 26)->where('estatus_id', 21)->first();
        $pathfirma     = RutasArchivo::where('tipo_id', 25)->where('estatus_id', 21)->first();
        $pathevidencia = RutasArchivo::where('tipo_id', 63)->where('estatus_id', 21)->first();

        Log::info($archivos);
        $carro     = $archivos->where('tipo_id', 26)->where('estatus_id', 21)->first();
        $firma     = $archivos->where('tipo_id', 25)->where('estatus_id', 21)->first();
        $evidencia = $archivos->where('tipo_id', 63)->where('estatus_id', 21)->values();

        $urls = [];

        if ($pathcarro && $carro) {
            $urls['carro'] = ['id' => $carro->id, 'url' => Storage::disk($pathcarro->disk)->url($pathcarro->folder . '/' . $carro->nombre)];
        } else {
            $urls['carro'] = null;
        }

        if ($pathfirma && $firma) {
            $urls['firma'] = ['id' => $firma->id, 'url' => Storage::disk($pathfirma->disk)->url($pathfirma->folder . '/' . $firma->nombre)];
        } else {
            $urls['firma'] = null;
        }

        if ($pathevidencia && $evidencia->isNotEmpty()) {
            $urls['evidencia'] = $evidencia->map(function ($item) use ($pathevidencia) {
                return ['id' => $item->id, 'url' => Storage::disk($pathevidencia->disk)
                    ->url($pathevidencia->folder . '/' . $item->nombre)
                    ];
            })->toArray();
        } else {
            $urls['evidencia'] = [];
        }
        return response()->json(compact('generales', 'pintura', 'interiores', 'exteriores', 'inventario', 'urls'));
    }
    public function GetOrdenesServicio(Request $request){
        $user=Auth::user();
        $currentPage=$request->currentPage ?? 1;
        $itemsPerPage=$request->itemsPerPage ?? 10;
        
        $query=OrdenesServicio::query()->with(['last_seguimiento','vehiculo.modelo.marca','empresa','ubicacion']);
        
        if(!$user->hasRole('Super Admin')){
            $user->load('modulos_orden');
            $modulosvisibles=$user->modulos_orden ? $user->modulos_orden->pluck('modulo_orden_id')->toarray(): [] ;
            $query->whereIn('modulo_orden_id',$modulosvisibles);
        }
            
        $query=$query->paginate($itemsPerPage,['*'],'page',$currentPage);
        $totalItems=$query->total();
        $items = $query->map(fn($item) => [
            'id' => $item->id,
            'orden' => $item->orden_servicio,
            'seguimiento' => $item->orden_seguimiento,
            'ubicacion' => optional($item->ubicacion)->nombre ?? null,
            'empresa' =>  optional($item->empresa)->nombre ?? null,
            'economico' => optional($item->vehiculo)->economico ?? null,
            'placas' => optional($item->vehiculo)->placas ?? null,
            'marca' => optional($item->vehiculo->modelo->marca)->descripcion ?? null,
            'modelo' => optional($item->vehiculo->modelo)->descripcion ?? null,
            'folios' => $item->folios ?? [], // ajusta según tu relación
            'creacion' => $item->created_at,
            'estatus' => $item->last_seguimiento ? 
                ($item->last_seguimiento->tipo_id===1 ? (
                    'Diagnostico Iniciado'
                )
                :'Revisando' ) 
                :'No Aplica',
            'upload_files'=>$item->cambiar_archivos
        ]);

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
        $keyspintura=['decolorada','color_desigual','rayones','grietas','golpes','emblemas','logos','rociado','granizo','lluvia'];
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
            'tipo_presupuesto_id'=>['required',new ExistTipo(2, $request->tipo_presupuesto_id ??'')],
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
            'indicaciones_cliente'=>['nullable','string'],
            'descripcion_mo'=>['nullable','string'],
            'folio'=>['nullable','string','max:20'],
            'inventario'=>['required','array'],
            'pintura'=>['required','array'],
            'condiciones_interiores'=>['required','array'],
            'condiciones_exteriores'=>['required','array'],
            'imagenes_evidencia'=>['required','array','min:6'],
            'imagenes_evidencia.*'=>['required','file','image','max:20048'],
            'carro'=>['required','file','image','max:2048'],
            'firma'=>['required','file','image','max:2048'],
            ...$rulesExtra,
        ]);
       
        $validator->after(function ($validator) use ($request, $user) {
            $modulosPermitidos = $user->modulos_orden->pluck('modulo_orden_id')->toArray();
            $vehiculosdisponiblesPermitidos = VehiculosConceptosDisponibles::where('modulo_orden_id',$request->modulo_orden_id)->pluck('vehiculo_concepto_id')->toArray();
            if ($request->filled('modulo_orden_id') && !(in_array($request->modulo_orden_id, $modulosPermitidos) || $request->user()->hasRole('Super Admin'))) {
                $validator->errors()->add('modulo_orden', 'El usuario no tiene permiso para este módulo de orden.');
            }
            if ($request->filled('vehiculo_concepto_id') && !(in_array($request->vehiculo_concepto_id, $vehiculosdisponiblesPermitidos))) {
                $validator->errors()->add('vehiculo_concepto_id','El Vehiculo NO Pertenece Al Modulo');
            }
        });
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        Log::info('pasa');
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
                'cambiar_archivos'=>false,
                'diagnostico'=>null,
                'indicaciones_cliente'=>$request->indicaciones_cliente ?? '',
                'notas_mecanico'=>$request->descripcion_mo ?? '',
                'notas_retraso'=>'',
                'telefono'=>$request->telefono,
                'ubicacion_id'=>$ubicacion->id,
            ]);
            DatosEntrada::create([
                'fecha'=>Carbon::now(),
                'estimacion'=>$request->estimacion,
                'kilomentraje'=>$request->kilometraje,
                'gasolina'=>$request->gasolina,
                'orden_servicio_id'=>$ordenservicio->id,
            ]);
            $administrador=UsuariosTaller::FirstOrCreate(['nombre'=>
                strtolower(trim($request->administrador)),
                'tipo_id'=>1
            ]);
            $jefetaller=UsuariosTaller::FirstOrCreate(['nombre'=>
                strtolower(trim($request->jefe)),
                'tipo_id'=>2
            ]);
            $tecnico=UsuariosTaller::FirstOrCreate(['nombre'=>
                strtolower(trim($request->tecnico)),
                'tipo_id'=>4
            ]);
            $trabajador=UsuariosTaller::FirstOrCreate(['nombre'=>
                strtolower(trim($request->trabajador)),
                'tipo_id'=>3
            ]);
            ResponsablesOrdenServicio::create([
                'administrador_transporte_id'=>$administrador->id,
                'jefe_de_proceso_id'=>$jefetaller->id,
                'trabajador_id'=>$trabajador->id,
                'tecnico_id'=>$tecnico->id,
                'orden_servicio_id'=>$ordenservicio->id,
            ]);
            $presupuesto=Presupuestos::create([
                'orden_servicio_id'=>$ordenservicio->id,
                'observaciones'=>'DE ACUERDO A LO DIFICIL DE LA FALLA PARA SU REPARACION',
                'descripcion_mo'=> $request->descripcion_mo ?? '',
                'garantia'=>'LO ESTIPULADO EN EL CONTRATO',
                'folio'=>$this->GetFolio($ordenservicio->id,$orden,$request->tipo_presupuesto_id,),
                'vigencia'=>Carbon::now(),
                'factura_id'=>null,
                'tipo_id'=>$request->tipo_presupuesto_id,
                'estatus_id'=>1,
            ]);
            InterioresOrdenServicio::create([
                'orden_servicio_id'=>$ordenservicio->id,
                'puerta_interior_frontal'=>$request->condiciones_interiores['puerta_izq_f'],
                'puerta_interior_trasera'=>$request->condiciones_interiores['puerta_izq_t'],
                'puerta_delantera_frontal'=>$request->condiciones_interiores['puerta_der_f'],
                'puerta_delantera_trasera'=>$request->condiciones_interiores['puerta_der_t'],
                'asiento_interior_frontal'=>$request->condiciones_interiores['asiento_izq_f'],
                'asiento_interior_trasera'=>$request->condiciones_interiores['asiento_izq_t'],
                'asiento_delantera_frontal'=>$request->condiciones_interiores['asiento_der_f'],
                'asiento_delantera_trasera'=>$request->condiciones_interiores['asiento_der_t'],
                'consola_central'=>$request->condiciones_interiores['consola'],
                'claxon'=>$request->condiciones_interiores['claxon'],
                'tablero'=>$request->condiciones_interiores['tablero'],
                'quemacocos'=>$request->condiciones_interiores['quemacocos'],
                'toldo'=>$request->condiciones_interiores['toldo'],
                'elevadores_eletricos'=>$request->condiciones_interiores['elevadores'],
                'luces_interiores'=>$request->condiciones_interiores['luces'],
                'seguros_eletricos'=>$request->condiciones_interiores['seguros'],
                'tapetes'=>$request->condiciones_interiores['tapetes'],
                'climatizador'=>$request->condiciones_interiores['climatizador'],
                'radio'=>$request->condiciones_interiores['radio'],
                'espejos_retrovizor'=>$request->condiciones_interiores['retrovisor'],
            ]);
            ExterioresOrdenServicio::create([
                'orden_servicio_id'=>$ordenservicio->id,
                'antena_radio'=>$request->condiciones_exteriores['antena_radio'],
                'antena_telefono'=>$request->condiciones_exteriores['antena_telefono'],
                'antena_cb'=>$request->condiciones_exteriores['antena_cb'],
                'estribos'=>$request->condiciones_exteriores['estribos'],
                'espejos_laterales'=>$request->condiciones_exteriores['espejos_laterales'],
                'guardafangos'=>$request->condiciones_exteriores['guardafangos'],
                'parabrisas'=>$request->condiciones_exteriores['parabrisas'],
                'sistema_alarma'=>$request->condiciones_exteriores['alarma'],
                'limpia_parabrisas'=>$request->condiciones_exteriores['limpiaparabrisas'],
                'luces_exteriores'=>$request->condiciones_exteriores['luces'],
            ]);
            InventarioOrdenServicio::create([
                'orden_servicio_id'=>$ordenservicio->id,
                'llanta'=>$request->inventario['llanta'],
                'cubreruedas'=>$request->inventario['cubreruedas'],
                'cables_corriente'=>$request->inventario['cables'],
                'candado_ruedas'=>$request->inventario['candado_rueda'],
                'estuche_herramientas'=>$request->inventario['estuche'],
                'gato'=>$request->inventario['gato'],
                'llave_tuercas'=>$request->inventario['llave_tuerca'],
                'trajeta_circulacion'=>$request->inventario['tarjeta_circulacion'],
                'triangulo_seguridad'=>$request->inventario['triangulo'],
                'extinguidor'=>$request->inventario['extinguidor'],
                'placas'=>$request->inventario['placas'],
            ]);

            CondicionesPinturaOrdenServicio::create([
                'orden_servicio_id'=>$ordenservicio->id,
                'decolorada'=>$request->pintura['decolorada'],
                'emblemas_completos'=>$request->pintura['emblemas'],
                'color_no_igual'=>$request->pintura['color_desigual'],
                'logos'=>$request->pintura['logos'],
                'exeso_rayones'=>$request->pintura['rayones'],
                'exeso_rociado'=>$request->pintura['rociado'],
                'pequenias_grietas'=>$request->pintura['grietas'],
                'danios_granizado'=>$request->pintura['granizo'],
                'carroceria_golpes'=>$request->pintura['golpes'],
                'lluvia_acido'=>$request->pintura['lluvia'],
            ]);
            
            $pathcarro     = RutasArchivo::where('tipo_id',26)->where('estatus_id',21)->first();
            $pathfirma     = RutasArchivo::where('tipo_id',25)->where('estatus_id',21)->first();
            $pathevidencia = RutasArchivo::where('tipo_id',63)->where('estatus_id',21)->first();
            $filessaves=[];
            if (!Storage::disk($pathcarro->disk ?? 'public')->exists($pathcarro->folder ?? 'desconocidos')) {
                    Storage::disk($pathcarro->disk ?? 'public')->makeDirectory($pathcarro->folder ?? 'desconocidos');
                }
            if (!Storage::disk($pathfirma->disk ?? 'public')->exists($pathfirma->folder ?? 'desconocidos')) {
                    Storage::disk($pathfirma->disk ?? 'public')->makeDirectory($pathfirma->folder ?? 'desconocidos');
                }
            if (!Storage::disk($pathevidencia->disk ?? 'public')->exists($pathevidencia->folder ?? 'desconocidos')) {
                    Storage::disk($pathevidencia->disk ?? 'public')->makeDirectory($pathevidencia->folder ?? 'desconocidos');
                }
            $ext = $request->file('carro')->getClientOriginalExtension();
            $request->file('carro')->storeAs(
                $pathcarro->folder ?? 'desconocidos',
                $orden.'_carro_detalles.'.$ext,
                $pathcarro->disk ?? 'public'
            );
            $filessaves[]=['folder'=>($pathcarro->folder ?? 'desconocidos'),'name'=>$orden.'_carro_detalles.'.$ext,'disk'=> $pathcarro->disk ?? 'public','tipo'=>26];
            $ext = $request->file('firma')->getClientOriginalExtension();
            
            $request->file('firma')->storeAs(
                $pathfirma->folder ?? 'desconocidos',
                $orden.'_firma.'.$ext,
                $pathfirma->disk ?? 'public'
            );
            $filessaves[]=['folder'=>($pathfirma->folder ?? 'desconocidos'),'name'=>$orden.'_firma.'.$ext,'disk'=> $pathfirma->disk ?? 'public','tipo'=>25];
            // Evidencias (array)
            foreach ($request->file('imagenes_evidencia') as $i => $file) {
                $ext = $file->getClientOriginalExtension();
                $file->storeAs(
                    $pathevidencia->folder ?? 'desconocidos',
                    $orden.'_evidencia_'.$i.'.'.$ext,
                    $pathevidencia->disk ?? 'public'
                );
                $filessaves[]=['folder'=>($pathevidencia->folder ?? 'desconocidos'),'name'=>$orden.'_evidencia_'.$i.'.'.$ext,'disk'=> $pathevidencia->disk ?? 'public','tipo'=>63];
            }

            foreach ($filessaves as $image){
                Archivos::create([
                    'nombre'=>$image['name'],
                    'orden_servicio_id'=>$ordenservicio->id,
                    'tipo_id'=>$image['tipo'],
                    'estatus_id'=>21
                ]);
            }
            DB::commit();
            return response()->json(['message' => 'Creada Correctamente']);
        }catch(\Exception $e){
            DB::rollBack();
            $imagenesdelete=$filessaves ?? [];
            foreach ($imagenesdelete as $img) {
                Storage::disk($img['disk'])->delete($img['name']);
            }
            return response()->json(['message' => 'Error al crear la orden de servicio: '.$e->getMessage()], 500);
        }
        
    }
    private function GetClave(string $modulo_orden_id){
        $anteriores=0;
        $clave=ModuloOrdenesServicio::find($modulo_orden_id);
        $num=OrdenesServicio::withTrashed()->where('modulo_orden_id',$modulo_orden_id)->count() + 1;
        do{
            $numeroConCeros = str_pad($num + $anteriores, 5, "0", STR_PAD_LEFT);
            $orden= ($clave->clave??'Desc').$numeroConCeros;
            $anteriores++;
        }while(OrdenesServicio::where('orden_servicio',$orden)->exists());
        return $orden;
    }
    private function GetFolio(string $ordenservicio_id,$clave,$tipo){

        $tipos=[5=>'C',6=>'P',7=>''];
        $num=Presupuestos::withTrashed()->where('orden_servicio_id',$ordenservicio_id)->count()  + 1;
        $numeroConCeros = str_pad($num, 2, "0", STR_PAD_LEFT);
        $folio= $clave.'-'.$numeroConCeros.$tipos[$tipo];
        return $folio;
    }
    public function ToggleFilesRecepcionVehicular(Request $request){
        $request->validate([
            'id'=>['required','exists:ordenes_servicio,id']
        ]);
        $ordenservicio=OrdenesServicio::find($request->id);
        $ordenservicio->cambiar_archivos=!$ordenservicio->cambiar_archivos;
        $ordenservicio->save();
        return response()->json(['message' => 'Actualizado Correctamente']);
    }
}

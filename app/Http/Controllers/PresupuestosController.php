<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Modelos;
use App\Models\Marcas;
use App\Models\ModuloOrdenesServicio;
use App\Models\OrdenesServicio;
use App\Models\Tipos;
use App\Models\UsuariosTaller;
use App\Models\Vehiculos;
use App\Services\Vehiculo;
use App\Models\ResponsablesOrdenServicio;
use App\Models\DatosEntrada;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Rules\ExistTipo;
use Illuminate\Http\Request;
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
        $validator=Validator::make($request,[
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

        if($request->filled('orden_servicio')){
            $orden=$request->orden_servicio;
        }else{
            $clave = ModuloOrdenesServicio::find($request->modulo_orden)->clave;
            $num=OrdenesServicio::where('modulo_orden_id', $request->modulo_orden)->count();
            do {
                $num++;
                $orden = $clave . str_pad($num, 6, '0', STR_PAD_LEFT);
                $existingOrder = OrdenesServicio::where('orden_servicio', $orden)->exists();
            }while ($existingOrder);    
        }

        $ordenservicio=OrdenesServicio::where('orden_servicio',$orden)->first();;
        if(!$ordenservicio){
            $vehiculo=Vehiculos::firstOrCreate([
                'economico' => $request->economico,
                'placas' => $request->placas,
            ], [
                'año' => $request->ubicacion,
                'vin' => $request->vin,
                'modelo_id' => Modelos::firstOrCreate([
                    'descripcion' => $request->modelo,
                    'marca_id' => Marcas::firstOrCreate(['descripcion' => $request->marca])->id,
                ])->id  ,
                'tipo_id' => 1,
                'color_id' => 1,
            ]);


            $ordenservicio=new OrdenesServicio();
            $ordenservicio->orden_servicio=$orden;
            $ordenservicio->orden_seguimiento=$request->orden_seguimiento??$orden;
            $ordenservicio->modulo_orden_id=$request->modulo_orden;
            $ordenservicio->vehiculo_id=$vehiculo->id;
            $ordenservicio->vehiculo_concepto_id=$request->vehiculo_concepto_id;
            $ordenservicio->user_id=$user->id;
            $ordenservicio->empresa_id=$request->empresa_id;
            $ordenservicio->cliente_id=$request->cliente_id;
            $ordenservicio->update_fotos=false;
            $ordenservicio->diagnostico=null;
            $ordenservicio->indicaciones_cliente=$request->indicaciones_cliente;
            $ordenservicio->notas_mecanico=$request->descripcion_mo;
            $ordenservicio->notas_retraso=$request->observaciones;
            $ordenservicio->telefono=$request->telefono;
            $ordenservicio->ubicacion=$request->ubicacion;
            $ordenservicio->save();

            // Crear datos de entrada
            $entrada = new DatosEntrada();
            $entrada->orden_servicio_id = $ordenservicio->id;
            $entrada->gasolina = $request->gasolina;
            $entrada->kilomentraje = $request->kilometraje;
            $entrada->estimacion = $request->estimacion;
            $entrada->save();

            $responsables = new ResponsablesOrdenServicio();
            $responsables->orden_servicio_id = $ordenservicio->id;
            $responsables->administrador_transporte_id = UsuariosTaller::firstOrCreate(['nombre'=>$request->administrador,'tipo_id'=>1])->id;
            $responsables->jefe_de_proceso_id = UsuariosTaller::firstOrCreate(['nombre'=>$request->jefe,'tipo_id'=>1])->id;
            $responsables->trabajador_id = UsuariosTaller::firstOrCreate(['nombre'=>$request->trabajador,'tipo_id'=>1])->id;
            $responsables->tecnico_id = UsuariosTaller::firstOrCreate(['nombre'=>$request->tecnico,'tipo_id'=>1])->id;

            $ExterioresEquipo=new \App\Models\ExterioresOrdenServicio();
            $EquipoInventario=new \App\Models\InventarioOrdenServicio();
            $InterioresEquipo=new \App\Models\InterioresOrdenServicio();
            $CondicionesPintura=new \App\Models\CondicionesPinturaOrdenServicio();
            
            $ExterioresEquipo->orden_servicio_id= $ordenservicio->id;
            $ExterioresEquipo->antena_radio = 3;
            $ExterioresEquipo->antena_telefono = 3;
            $ExterioresEquipo->antena_cb = 3;
            $ExterioresEquipo->estribos = 3;
            $ExterioresEquipo->espejos_laterales = 3;
            $ExterioresEquipo->guardafangos = 3;
            $ExterioresEquipo->parabrisas = 3;
            $ExterioresEquipo->sistema_alarma = 3;
            $ExterioresEquipo->limpia_parabrisas = 3;
            $ExterioresEquipo->luces_exteriores = 3;
            $ExterioresEquipo->save();


            $EquipoInventario->orden_servicio_id= $ordenservicio->id;
            $EquipoInventario->llanta = 0;
            $EquipoInventario->cubreruedas = 0;
            $EquipoInventario->cables_corriente = 0;
            $EquipoInventario->candado_ruedas = 0;
            $EquipoInventario->estuche_herramientas = 0;
            $EquipoInventario->gato = 0;
            $EquipoInventario->llave_tuercas =0;
            $EquipoInventario->tarjeta_circulacion = 0;
            $EquipoInventario->triangulo_seguridad = 0;
            $EquipoInventario->extinguidor = 0;
            $EquipoInventario->placas = 0;
            $EquipoInventario->save();


            $InterioresEquipo->orden_servicio_id= $ordenservicio->id;
            $InterioresEquipo->puerta_interior_frontal = 3;
            $InterioresEquipo->puerta_interior_trasera = 3;
            $InterioresEquipo->puerta_delantera_frontal = 3;
            $InterioresEquipo->puerta_delantera_trasera = 3;
            $InterioresEquipo->asiento_interior_frontal = 3;
            $InterioresEquipo->asiento_interior_trasera = 3;
            $InterioresEquipo->asiento_delantera_frontal = 3;
            $InterioresEquipo->asiento_delantera_trasera = 3;
            $InterioresEquipo->consola_central = 3;
            $InterioresEquipo->claxon = 3;
            $InterioresEquipo->tablero = 3;
            $InterioresEquipo->quemacocos = 3;
            $InterioresEquipo->toldo = 3;
            $InterioresEquipo->elevadores_eletricos =3;
            $InterioresEquipo->luces_interiores = 3;
            $InterioresEquipo->seguros_eletricos = 3;
            $InterioresEquipo->tapetes = 3;
            $InterioresEquipo->climatizador = 3;
            $InterioresEquipo->radio = 3;
            $InterioresEquipo->espejos_retrovizor =3;
            $InterioresEquipo->save();

            $CondicionesPintura->orden_servicio_id= $ordenservicio->id;
            $CondicionesPintura->decolorada=0;
            $CondicionesPintura->emblemas_completos=0;
            $CondicionesPintura->color_no_igual=0;
            $CondicionesPintura->logos=0;
            $CondicionesPintura->exeso_rayones=0;
            $CondicionesPintura->exeso_rociado=0;
            $CondicionesPintura->pequenias_grietas=0;
            $CondicionesPintura->danios_granizado=0;
            $CondicionesPintura->carroceria_golpes=0;
            $CondicionesPintura->lluvia_acido=0;
            $CondicionesPintura->save();

        }
        
        
        return response()->json(['message' => 'Presupuesto creado exitosamente', 'orden_servicio' => $ordenservicio->orden_servicio], 201);
    }
}

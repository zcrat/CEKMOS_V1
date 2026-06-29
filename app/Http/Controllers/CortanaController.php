<?php

namespace App\Http\Controllers;

use App\Events\OrdenServicioEvents;
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
use App\Models\RecepcionesVehiculares;
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
use Illuminate\Validation\Rule;
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
    public function GetOrdenServicio(Request $request){
        $request->validate([
            'id'=>['required','exists:ordenes_servicio,id']
        ]);
        $ordenservicio=OrdenesServicio::with( [
                'interiores', 
                'exteriores', 
                'inventario', 
                'condiciones_pintura',
                'recepcion_vehicular',
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
            'orden_opcional'=>$ordenservicio->orden_opcional ?? '',
            'ubicacion'=>$ordenservicio->ubicacion->nombre ,
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
            'descripcion_mo'=>$ordenservicio->fallas_reportadas,
            'indicaciones_cliente'=>$ordenservicio->recepcion_vehicular->indicaciones_cliente ?? '',
            'cambiar_archivos'=>$ordenservicio->recepcion_vehicular->cambiar_archivos ?? false,
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
                return ['id' => $item->id, 'url' => Storage::disk($pathevidencia->disk)->url($pathevidencia->folder . '/' . $item->nombre)
                    ];
            })->toArray();
        } else {
            $urls['evidencia'] = [];
        }
        return response()->json(compact('generales', 'pintura', 'interiores', 'exteriores', 'inventario', 'urls'));
    }
}

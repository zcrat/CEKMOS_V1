<?php

namespace App\Http\Controllers;

use App\Models\OrdenesServicio;
use App\Services\AlcanceRecepcionesVehiculares;
use Carbon\Carbon;
use Spatie\LaravelPdf\Facades\Pdf;
use App\Models\RutasArchivo;
use App\Models\Estatus;
use App\Models\InspeccionVehicular;
use Illuminate\Support\Facades\Storage;

class PdfController extends Controller
{
    private const ESTATUS_INSPECCION_IDS = [
        'inmediata' => 23,
        'futura' => 24,
        'bien' => 25,
    ];

    public function RecepcionVehicular($id, bool $renderBlade = false)
    {
        $ordenservicio=OrdenesServicio::with([
            'cliente',
            'user',
            'empresa.municipio.estado',
            'ubicacion',
            'responsables.tecnico',
            'entrada.nivel_combustible',
            'salida.nivel_combustible',
            'vehiculo.color',
            'vehiculo.modelo.marca',
            'recepcion_vehicular.interiores',
            'recepcion_vehicular.exteriores',
            'recepcion_vehicular.inventario',
            'recepcion_vehicular.condiciones_pintura',
            'recepcion_vehicular.archivos',
            // Emisor ahora se obtiene desde ModuloOrdenesServicio
            'modulo_ordenes_servicio.emisor',
        ])->find($id);
        if(!$ordenservicio){
            return  Pdf::html('<h1>Orden de servicio no encontrada</h1>')->format('A4');
        }
        abort_unless(
            AlcanceRecepcionesVehiculares::puedeAccederOrden(
                request()->user(),
                $ordenservicio
            ),
            403
        );

        $recepcionVehicular=$ordenservicio->recepcion_vehicular;
        $interiores=$recepcionVehicular?->interiores;
        $exteriores=$recepcionVehicular?->exteriores;
        $inventario=$recepcionVehicular?->inventario;
        $condicionesPintura=$recepcionVehicular?->condiciones_pintura;
        $archivos=$recepcionVehicular?->archivos ?? collect([]);
        $carro=$archivos->where('tipo_id',26)->first();
        $firma=$archivos->where('tipo_id',25)->first();
        
        $carro_url=null;
        $firma_url=null;
        $firma_recibido_url=null;
        $firma_cliente_url=null;
        $rutas_archivos=RutasArchivo::whereIn('tipo_id',[25,26])->get()->keyBy('tipo_id');
        if($carro){
            $ruta=$rutas_archivos->get(26);
            $carro_url=Storage::url($ruta->folder.'/'.$carro->nombre);
        }
        if($firma){
            $ruta=$rutas_archivos->get(25);
            $firma_url=Storage::url($ruta->folder.'/'.$firma->nombre);
        }
        // Datos del emisor (empresa que emite) usando la relación Emisor
        $emisor = optional(optional($ordenservicio->modulo_ordenes_servicio)->emisor);
        // Construcción en una sola línea con el formato:
        // "{NOMBRE} {CALLE}, {COLONIA}. C.P. {CP}, {CIUDAD}, {ESTADO}, TEL {TELEFONO}"
        $direccion_emisor = '';
        if ($emisor) {
            $bloques = [];
            if (!empty($emisor->calle)) { $bloques[] = $emisor->calle; }
            // Colonia sin el prefijo "COL.", seguido de punto, y CP
            $col_cp = '';
            if (!empty($emisor->colonia)) { $col_cp .= $emisor->colonia . '. '; }
            if (!empty($emisor->cp)) { $col_cp .= 'C.P. ' . $emisor->cp; }
            $col_cp = trim($col_cp);
            if (!empty($col_cp)) { $bloques[] = $col_cp; }
            if (!empty($emisor->ciudad)) { $bloques[] = $emisor->ciudad; }
            if (!empty($emisor->estado)) { $bloques[] = $emisor->estado; }
            if (!empty($emisor->telefono)) { $bloques[] = 'TEL ' . $emisor->telefono; }
            $direccion_detalle = implode(', ', array_filter($bloques));
            $prefijo_nombre = !empty($emisor->nombre) ? trim($emisor->nombre) . ' ' : '';
            $direccion_emisor = trim($prefijo_nombre . $direccion_detalle);
        }

        $viewData = [
            'datos' => [
                'telefono'=>$ordenservicio->telefono,
                'cliente'=>$ordenservicio->cliente->nombre,
                'seguimiento'=>$ordenservicio->orden_seguimiento,
                'orden'=>$ordenservicio->orden_servicio,
                'ubicacion'=>$ordenservicio->ubicacion->descripcion,
                'usuario'=>$ordenservicio->user->name,
                'tecnico'=>$ordenservicio->responsables->tecnico->nombre ?? '',
                'notas'=>$ordenservicio->fallas_reportadas,
                'observaciones'=>$recepcionVehicular?->indicaciones_cliente ?? '',
            ],
            'interiores'=>[
                'pif'=>$interiores?->puerta_interior_frontal ?? '0',
                'pdf'=>$interiores?->puerta_delantera_frontal ?? '0',
                'pit'=>$interiores?->puerta_interior_trasera ?? '0',
                'pdt'=>$interiores?->puerta_delantera_trasera ?? '0',
                'aif'=>$interiores?->asiento_interior_frontal ?? '0',
                'adf'=>$interiores?->asiento_delantera_frontal ?? '0',
                'ait'=>$interiores?->asiento_interior_trasera ?? '0',
                'adt'=>$interiores?->asiento_delantera_trasera ?? '0',
                'consola'=>$interiores?->consola_central ?? '0',
                'claxon'=>$interiores?->claxon ?? '0',
                'tablero'=>$interiores?->tablero ?? '0',
                'quemacocos'=>$interiores?->quemacocos ?? '0',
                'toldo'=>$interiores?->toldo ?? '0',
                'tapetes'=>$interiores?->tapetes ?? '0',
                'radio'=>$interiores?->radio ?? '0',
                'retrovisor'=>$interiores?->espejos_retrovizor ?? '0',
                'luces_interior'=>$interiores?->luces_interiores ?? '0',
                'seguros_electricos'=>$interiores?->seguros_eletricos ?? '0',
                'elevadores_electricos'=>$interiores?->elevadores_eletricos ?? '0',
                'climatizador'=>$interiores?->climatizador ?? '0',
            ],
            'exteriores'=>[
                'antena_radio'=>$exteriores?->antena_radio ?? '0',
                'estribos'=>$exteriores?->estribos ?? '0',
                'antena_telefono'=>$exteriores?->antena_telefono ?? '0',
                'guardafangos'=>$exteriores?->guardafangos ?? '0',
                'antena_cb'=>$exteriores?->antena_cb ?? '0',
                'parabrisas'=>$exteriores?->parabrisas ?? '0',
                'sistema_alarma'=>$exteriores?->sistema_alarma ?? '0',
                'limpiaparabrisas'=>$exteriores?->limpia_parabrisas ?? '0',
                'luces_exteriores'=>$exteriores?->luces_exteriores ?? '0',
                'espejos_laterales'=>$exteriores?->espejos_laterales ?? '0',
            ],
            'inventario'=>[
                'gato'=>$inventario?->gato ? '1' : '0',
                'extinguidor'=>$inventario?->extinguidor ? '1' : '0',
                'placas'=>$inventario?->placas ? '1' : '0',
                'llantas_refaccion'=>$inventario?->llanta ? '1' : '0',
                'cubreruedas'=>$inventario?->cubreruedas ? '1' : '0',
                'candado_ruedas'=>$inventario?->candado_ruedas ? '1' : '0',
                'llave_tuercas'=>$inventario?->llave_tuercas ? '1' : '0',
                'triangulo_seguridad'=>$inventario?->triangulo_seguridad ? '1' : '0',
                'cables_corriente'=>$inventario?->cables_corriente ? '1' : '0',
                'estuche_herramientas'=>$inventario?->estuche_herramientas ? '1' : '0',
                'tarjeta_circulacion'=>$inventario?->trajeta_circulacion ? '1' : '0',
            ],
            'pintura'=>[
                'decolorada'=>$condicionesPintura?->decolorada ? '1' : '0',
                'exceso_rociado'=>$condicionesPintura?->exeso_rociado ? '1' : '0',
                'color_no_igualado'=>$condicionesPintura?->color_no_igual ? '1' : '0',
                'logos_buen_estado'=>$condicionesPintura?->logos ? '1' : '0',
                'exceso_rayones'=>$condicionesPintura?->exeso_rayones ? '1' : '0',
                'lluvia_acida'=>$condicionesPintura?->lluvia_acido ? '1' : '0',
                'danos_granizo'=>$condicionesPintura?->danios_granizado ? '1' : '0',
                'pequenas_grietas'=>$condicionesPintura?->pequenias_grietas ? '1' : '0',
                'carroceria_golpes'=>$condicionesPintura?->carroceria_golpes ? '1' : '0',
                'emblemas_completos'=>$condicionesPintura?->emblemas_completos ? '1' : '0',
            ],
            'empresa'=>[
                'nombre'=>$ordenservicio->empresa->nombre,
                'calle'=>$ordenservicio->empresa->calle,
                'ciudad'=>$ordenservicio->empresa->municipio->nombre,
                'estado'=>$ordenservicio->empresa->municipio->estado->nombre,
                'cp'=>$ordenservicio->empresa->cp,
                'negocio'=>$ordenservicio->empresa->tel_negocio,
                'casa'=>$ordenservicio->empresa->tel_celular,
                'email'=>$ordenservicio->empresa->email,
            ],
            'entrada'=>[
                'fecha'=>Carbon::parse($ordenservicio->entrada->fecha)->format('d/m/Y'),
                'gasolina'=>$ordenservicio->entrada->nivel_combustible->descripcion,
                'estimacion'=>$ordenservicio->entrada->estimacion,
                'kilometraje'=>$ordenservicio->entrada->kilometraje,
            ],
            'salida'=>[
                'fecha'=>Carbon::parse($ordenservicio->salida->fecha)->format('d/m/Y'),
                'gasolina'=>$ordenservicio->salida->nivel_combustible->descripcion,
                'kilometraje'=>$ordenservicio->salida->kilometraje,
            ],
            'vehiculo'=>[
                'anio'=>$ordenservicio->vehiculo->año,
                'marca'=>$ordenservicio->vehiculo->modelo->marca->descripcion,
                'modelo'=>$ordenservicio->vehiculo->modelo->descripcion,
                'placas'=>$ordenservicio->vehiculo->placas,
                'color'=>$ordenservicio->vehiculo->color->descripcion,
                'economico'=>$ordenservicio->vehiculo->economico,
                'vin'=>$ordenservicio->vehiculo->vin
            ],
            'empresa_emision'=>[
                'logo' => $emisor->logotipo ?? 'desconocido.png',
                'direccion' => $direccion_emisor ?: ($emisor->nombre ?? ''),
            ],
            'carro' => $carro_url,
            'firma' => $firma_url,
            'firma_recibido' => $firma_recibido_url,
            'firma_cliente' => $firma_cliente_url,
        ];

        if ($renderBlade) {
            return view('pdf.RecepcionVehicular', $viewData);
        }

        return Pdf::view('pdf.RecepcionVehicular', $viewData)->format('A4');
    }
    public function InspeccionVehicular($id, bool $renderBlade = false)
    {
        $ordenservicio=OrdenesServicio::with([
            'empresa',
            'entrada',
            'vehiculo.modelo.marca',
            'recepcion_vehicular.archivos',
            'modulo_ordenes_servicio.emisor',
        ])->find($id);
        if(!$ordenservicio){
            return  Pdf::html('<h1>Orden de servicio no encontrada</h1>')->format('A4');
        }
        abort_unless(
            AlcanceRecepcionesVehiculares::puedeAccederOrden(
                request()->user(),
                $ordenservicio
            ),
            403
        );

        [$inspeccion, $estatusInspeccion] = $this->datosInspeccionVehicular($ordenservicio->id);

        $recepcionVehicular=$ordenservicio->recepcion_vehicular;
        $archivos=$recepcionVehicular?->archivos ?? collect([]);
        $carro=$archivos->where('tipo_id',26)->first();

        $carro_url=null;
        if($carro){
            $rutaCarro=RutasArchivo::where('tipo_id',26)->first();
            $carro_url=Storage::url(($rutaCarro->folder ?? 'desconocidos').'/'.$carro->nombre);
        }
        // Datos del emisor (empresa que emite) usando la relación Emisor
        $emisor = optional(optional($ordenservicio->modulo_ordenes_servicio)->emisor);
        // Construcción en una sola línea con el formato:
        // "{NOMBRE} {CALLE}, {COLONIA}. C.P. {CP}, {CIUDAD}, {ESTADO}, TEL {TELEFONO}"
        $direccion_emisor = '';
        if ($emisor) {
            $bloques = [];
            if (!empty($emisor->calle)) { $bloques[] = $emisor->calle; }
            // Colonia sin el prefijo "COL.", seguido de punto, y CP
            $col_cp = '';
            if (!empty($emisor->colonia)) { $col_cp .= $emisor->colonia . '. '; }
            if (!empty($emisor->cp)) { $col_cp .= 'C.P. ' . $emisor->cp; }
            $col_cp = trim($col_cp);
            if (!empty($col_cp)) { $bloques[] = $col_cp; }
            if (!empty($emisor->ciudad)) { $bloques[] = $emisor->ciudad; }
            if (!empty($emisor->estado)) { $bloques[] = $emisor->estado; }
            if (!empty($emisor->telefono)) { $bloques[] = 'TEL ' . $emisor->telefono; }
            $direccion_detalle = implode(', ', array_filter($bloques));
            $prefijo_nombre = !empty($emisor->nombre) ? trim($emisor->nombre) . ' ' : '';
            $direccion_emisor = trim($prefijo_nombre . $direccion_detalle);
        }

        $viewData = [
            'datos' => [
                'telefono'=>$ordenservicio->telefono,
                'orden'=>$ordenservicio->orden_servicio,
                'observaciones'=>$recepcionVehicular?->indicaciones_cliente ?? '',
            ],
            'empresa'=>[
                'nombre'=>$ordenservicio->empresa->nombre,
            ],
            'entrada'=>[
                'fecha'=>Carbon::parse($ordenservicio->entrada->fecha)->format('d/m/Y'),
                'kilometraje'=>$ordenservicio->entrada->kilometraje,
            ],
            'vehiculo'=>[
                'anio'=>$ordenservicio->vehiculo->año,
                'marca'=>$ordenservicio->vehiculo->modelo->marca->descripcion,
                'modelo'=>$ordenservicio->vehiculo->modelo->descripcion,
                'placas'=>$ordenservicio->vehiculo->placas,
                'economico'=>$ordenservicio->vehiculo->economico,
                'vin'=>$ordenservicio->vehiculo->vin
            ],
            'empresa_emision'=>[
                'logo' => $emisor->logotipo ?? 'desconocido.png',
                'direccion' => $direccion_emisor ?: ($emisor->nombre ?? ''),
            ],
            'carro' => $carro_url,
            'inspeccion' => $inspeccion,
            'estatus_inspeccion' => $estatusInspeccion,
        ];
        if ($renderBlade) {
            return view('pdf.InspeccionVehicular', $viewData);
        }

        return Pdf::view('pdf.InspeccionVehicular', $viewData)->format('A4');
    }

    private function datosInspeccionVehicular(int $ordenServicioId): array
    {
        $inspeccion = InspeccionVehicular::with([
            'llantas',
            'liquidos',
            'bandas',
            'seguridad',
            'filtros',
            'escape',
            'suspencion_direccion',
            'afinacion_motor',
            'tren_transmision',
            'frenos',
            'electrico',
            'luces_espias',
            'mangueras',
        ])->where('orden_servicio_id', $ordenServicioId)->first();

        $descripciones = Estatus::whereIn('id', array_values(self::ESTATUS_INSPECCION_IDS))
            ->pluck('descripcion', 'id');

        $estatus = collect(self::ESTATUS_INSPECCION_IDS)->mapWithKeys(
            fn ($id, $tipo) => [
                $id => [
                    'tipo' => $tipo,
                    'descripcion' => $descripciones->get($id, ''),
                ],
            ]
        )->all();

        return [$inspeccion, $estatus];
    }
}

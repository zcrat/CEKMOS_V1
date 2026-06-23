<?php

namespace App\Http\Controllers;

use App\Models\OrdenesServicio;
use Carbon\Carbon;
use Spatie\LaravelPdf\Facades\Pdf;
use App\Models\RutasArchivo;
use Illuminate\Support\Facades\Storage;

class PdfController extends Controller
{
    public function RecepcionVehicular($id)
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
            'interiores',
            'exteriores',
            'inventario',
            'condiciones_pintura',
            'archivos',
            // Emisor ahora se obtiene desde ModuloOrdenesServicio
            'modulo_ordenes_servicio.emisor',
        ])->find($id);
        if(!$ordenservicio){
            return  Pdf::html('<h1>Orden de servicio no encontrada</h1>')->format('A4');
        }
        $archivos=$ordenservicio->archivos ?? collect([]);
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

        return Pdf::view('pdf.RecepcionVehicular',[
            'datos' => [
                'telefono'=>$ordenservicio->telefono,
                'cliente'=>$ordenservicio->cliente->nombre,
                'seguimiento'=>$ordenservicio->orden_seguimiento,
                'orden'=>$ordenservicio->orden_servicio,
                'ubicacion'=>$ordenservicio->ubicacion->descripcion,
                'usuario'=>$ordenservicio->user->name,
                'tecnico'=>$ordenservicio->responsables->tecnico->nombre ?? '',
                'notas'=>$ordenservicio->notas_mecanico,
                'observaciones'=>$ordenservicio->indicaciones_cliente,
            ],
            'interiores'=>[
                'pif'=>$ordenservicio->interiores->puerta_interior_frontal ?? '0',
                'pdf'=>$ordenservicio->interiores->puerta_delantera_frontal ?? '0',
                'pit'=>$ordenservicio->interiores->puerta_interior_trasera ?? '0',
                'pdt'=>$ordenservicio->interiores->puerta_delantera_trasera ?? '0',
                'aif'=>$ordenservicio->interiores->asiento_interior_frontal ?? '0',
                'adf'=>$ordenservicio->interiores->asiento_delantera_frontal ?? '0',
                'ait'=>$ordenservicio->interiores->asiento_interior_trasera ?? '0',
                'adt'=>$ordenservicio->interiores->asiento_delantera_trasera ?? '0',
                'consola'=>$ordenservicio->interiores->consola_central ?? '0',
                'claxon'=>$ordenservicio->interiores->claxon ?? '0',
                'tablero'=>$ordenservicio->interiores->tablero ?? '0',
                'quemacocos'=>$ordenservicio->interiores->quemacocos ?? '0',
                'toldo'=>$ordenservicio->interiores->toldo ?? '0',
                'tapetes'=>$ordenservicio->interiores->tapetes ?? '0',
                'radio'=>$ordenservicio->interiores->radio ?? '0',
                'retrovisor'=>$ordenservicio->interiores->espejos_retrovizor ?? '0',
                'luces_interior'=>$ordenservicio->interiores->luces_interiores ?? '0',
                'seguros_electricos'=>$ordenservicio->interiores->seguros_eletricos ?? '0',
                'elevadores_electricos'=>$ordenservicio->interiores->elevadores_eletricos ?? '0',
                'climatizador'=>$ordenservicio->interiores->climatizador ?? '0',
            ],
            'exteriores'=>[
                'antena_radio'=>$ordenservicio->exteriores->antena_radio ?? '0',
                'estribos'=>$ordenservicio->exteriores->estribos ?? '0',
                'antena_telefono'=>$ordenservicio->exteriores->antena_telefono ?? '0',
                'guardafangos'=>$ordenservicio->exteriores->guardafangos ?? '0',
                'antena_cb'=>$ordenservicio->exteriores->antena_cb ?? '0',
                'parabrisas'=>$ordenservicio->exteriores->parabrisas ?? '0',
                'sistema_alarma'=>$ordenservicio->exteriores->sistema_alarma ?? '0',
                'limpiaparabrisas'=>$ordenservicio->exteriores->limpia_parabrisas ?? '0',
                'luces_exteriores'=>$ordenservicio->exteriores->luces_exteriores ?? '0',
                'espejos_laterales'=>$ordenservicio->exteriores->espejos_laterales ?? '0',
            ],
            'inventario'=>[
                'gato'=>$ordenservicio->inventario->gato ? '1' : '0',
                'extinguidor'=>$ordenservicio->inventario->extinguidor ? '1' : '0',
                'placas'=>$ordenservicio->inventario->placas ? '1' : '0',
                'llantas_refaccion'=>$ordenservicio->inventario->llanta ? '1' : '0',
                'cubreruedas'=>$ordenservicio->inventario->cubreruedas ? '1' : '0',
                'candado_ruedas'=>$ordenservicio->inventario->candado_ruedas ? '1' : '0',
                'llave_tuercas'=>$ordenservicio->inventario->llave_tuercas ? '1' : '0',
                'triangulo_seguridad'=>$ordenservicio->inventario->triangulo_seguridad ? '1' : '0',
                'cables_corriente'=>$ordenservicio->inventario->cables_corriente ? '1' : '0',
                'estuche_herramientas'=>$ordenservicio->inventario->estuche_herramientas ? '1' : '0',
                'tarjeta_circulacion'=>$ordenservicio->inventario->trajeta_circulacion ? '1' : '0',
            ],
            'pintura'=>[
                'decolorada'=>$ordenservicio->condiciones_pintura->decolorada ? '1' : '0',
                'exceso_rociado'=>$ordenservicio->condiciones_pintura->exeso_rociado ? '1' : '0',
                'color_no_igualado'=>$ordenservicio->condiciones_pintura->color_no_igual ? '1' : '0',
                'logos_buen_estado'=>$ordenservicio->condiciones_pintura->logos ? '1' : '0',
                'exceso_rayones'=>$ordenservicio->condiciones_pintura->exeso_rayones ? '1' : '0',
                'lluvia_acida'=>$ordenservicio->condiciones_pintura->lluvia_acido ? '1' : '0',
                'danos_granizo'=>$ordenservicio->condiciones_pintura->danios_granizado ? '1' : '0',
                'pequenas_grietas'=>$ordenservicio->condiciones_pintura->pequenias_grietas ? '1' : '0',
                'carroceria_golpes'=>$ordenservicio->condiciones_pintura->carroceria_golpes ? '1' : '0',
                'emblemas_completos'=>$ordenservicio->condiciones_pintura->emblemas_completos ? '1' : '0',
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
                'marca'=>$ordenservicio->vehiculo->marca->descripcion,
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
            
        ])->format('A4');
    }
}

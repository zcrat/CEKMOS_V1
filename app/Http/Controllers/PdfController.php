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
        $ordenservicio=OrdenesServicio::with(['cliente','user','empresa.municipio.estado',
        'ubicacion','ResponsablesOrdenServicio.tecnico','entrada.nivel_combustible','salida.nivel_combustible',
                                                'vehiculo.color',
                                                'vehiculo.modelo.marca',
                                                'archivos'])->find($id);
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
        
        return Pdf::view('pdf.RecepcionVehicular',[
            'datos' => [
                'telefono'=>$ordenservicio->telefono,
                'cliente'=>$ordenservicio->cliente->nombre,
                'seguimiento'=>$ordenservicio->orden_seguimiento,
                'orden'=>$ordenservicio->orden_servicio,
                'ubicacion'=>$ordenservicio->ubicacion->descripcion,
                'usuario'=>$ordenservicio->user->name,
                'tecnico'=>$ordenservicio->ResponsablesOrdenServicio->tecnico->nombre,
                'notas'=>$ordenservicio->notas_mecanico,
                'observaciones'=>$ordenservicio->indicaciones_cliente,
            ],
            'interiores'=>[
                'pif'=>'2'
            ],
            'exteriores'=>[
                'antena'=>'2'
            ],
            'inventario'=>[
                'gato'=>'1',
            ],
            'pintura'=>[
                'decolorada'=>'0'
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
                'logo'=>'logo_akumas.png',
                'direccion'=>'ECO IMPULSA, S.A. DE .C.V. PUERTO DE ACAPULCO #328, RINCON DEL ANGEL. C.P. 58337, MORELIA, MICH, TEL (433) 2532182'
            ],
            'carro' => $carro_url,
            'firma' => $firma_url,
            'firma_recibido' => $firma_recibido_url,
            'firma_cliente' => $firma_cliente_url,
            
        ])->format('A4');
    }
}

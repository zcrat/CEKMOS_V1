<?php

namespace App\Http\Controllers;

use Spatie\LaravelPdf\Facades\Pdf;
use Spatie\Browsershot\Browsershot;

class PdfController extends Controller
{
    public function show()
    {
       
        return Pdf::view('pdf.RecepcionVehicular',[
            'datos' => [
                'telefono'=>'',
                'cliente'=>'Prueba',
                'seguimiento'=>'Prueba',
                'orden'=>'Prueba',
                'ubicacion'=>'Prueba',
                'usuario'=>'Prueba',
                'tecnico'=>'',
                'notas'=>'',
                'observaciones'=>''
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
                'nombre'=>'prueba',
                'calle'=>'prueba',
                'ciudad'=>'prueba',
                'estado'=>'prueba',
                'cp'=>'prueba',
                'negocio'=>'prueba',
                'casa'=>'prueba',
                'email'=>'prueba',
            ],
            'entrada'=>[
                'fecha'=>'',
                'gasolina'=>'',
                'estimacion'=>'',
                'kilometraje'=>'',
            ],
            'salida'=>[
                'fecha'=>'',
                'gasolina'=>'',
                'kilometraje'=>'',
            ],
            'vehiculo'=>[
                'anio'=>'',
                'marca'=>'',
                'modelo'=>'',
                'placas'=>'',
                'color'=>'',
                'economico'=>'',
                'vin'=>''
            ],
            'empresa_emision'=>[
                'logo'=>'logo_akumas.png',
                'direccion'=>'ECO IMPULSA, S.A. DE .C.V. PUERTO DE ACAPULCO #328, RINCON DEL ANGEL. C.P. 58337, MORELIA, MICH, TEL (433) 2532182'
            ],
            'carro' => 'storage/archivos/ordenes_servicio/carros/ALT00003_carro_detalles.jpeg',
            'firma' => 'storage/archivos/ordenes_servicio/firmas/ALT00003_firma.jpeg',
            'firma_recibido' => 'storage/archivos/ordenes_servicio/firmas/ALT00003_firma.jpeg',
            'firma_cliente' => 'storage/archivos/ordenes_servicio/firmas/ALT00003_firma.jpeg',
            
        ])->format('A4');
    }
}

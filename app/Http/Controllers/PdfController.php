<?php

namespace App\Http\Controllers;

use Spatie\LaravelPdf\Facades\Pdf;
use Spatie\Browsershot\Browsershot;

class PdfController extends Controller
{
    public function show()
    {
       return Pdf::view('pdf.RecepcionVehicular',[
            'orden' => 'prueba',
            'usuario' => 'Ivan',
            'items' => [
                ['nombre' => 'Aceite', 'estado' => 'OK'],
                ['nombre' => 'Frenos', 'estado' => 'Pendiente'],
                ['nombre' => 'Llantas', 'estado' => 'OK'],
            ]
        ])->format('A4');
    }
}

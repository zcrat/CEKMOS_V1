<?php

namespace App\Http\Controllers;

use Spatie\LaravelPdf\Facades\Pdf;
use Spatie\Browsershot\Browsershot;

class PdfController extends Controller
{
    public function show()
    {
        Pdf::html('<h1>Hello world</h1>')->save('my-a3-pdf.pdf');
        return 'funciona';
    }
}

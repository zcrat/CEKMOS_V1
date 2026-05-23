<?php

namespace App\Http\Controllers;

use Spatie\LaravelPdf\Facades\Pdf;
use Spatie\Browsershot\Browsershot;

class PdfController extends Controller
{
    public function show()
    {
        Pdf::html('<h1>Hello world</h1>')->withBrowsershot(function (Browsershot $browsershot) {
            $browsershot->setOption('args', [
                '--disable-web-security',
                '--allow-file-access-from-files',
            ]);
        })->save('my-a3-pdf.pdf');
        return 'funciona';
    }
}

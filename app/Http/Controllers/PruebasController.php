<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
class PruebasController extends Controller
{
    public function TransformDataToImport(Request $request)
    {
        ini_set('memory_limit', '1024M');
        set_time_limit(0);

        Log::info('entra');

        try {

            $pathorigin = Storage::disk('public')->path('pruebas/files/conceptos1.xlsx');

            // 1️⃣ Obtener nombres de hojas (NO carga el archivo completo)
            $reader = IOFactory::createReader('Xlsx');
            $sheetNames = $reader->listWorksheetNames($pathorigin);

            Log::info('Hojas detectadas: ' . count($sheetNames));

            // 2️⃣ Crear archivo destino
            $spreadsheet = new Spreadsheet();
            $sheetDest = $spreadsheet->getActiveSheet();

            // Encabezados
            $headers = [
                'NUM','TIPO','DESCRIPCION','MO','REFACCION','TOTAL',
                'CATEGORIA','SAT','UNIDAD','MODULO','CONTRATO','ZONA','ANIO'
            ];

            foreach ($headers as $i => $header) {
                $sheetDest->setCellValue(chr(65 + $i) . '1', $header);
            }

            $filaNum = 2;

            // 3️⃣ Procesar hoja por hoja
            $reader->setReadDataOnly(true);

            foreach ($sheetNames as $sheetName) {

                Log::info("Procesando hoja: {$sheetName}");

                $reader->setLoadSheetsOnly([$sheetName]);
                $spreadsheetOrigen = $reader->load($pathorigin);
                $sheetOrigen = $spreadsheetOrigen->getActiveSheet();

                $index = 0;

                foreach ($sheetOrigen->getRowIterator() as $row) {

                    if ($index++ === 0) continue; // saltar encabezado

                    $cells = [];
                    foreach ($row->getCellIterator() as $cell) {
                        $cells[] = trim((string) $cell->getFormattedValue());
                    }

                    if (empty(array_filter($cells))) continue;

                    $sheetDest->setCellValue('A'.$filaNum, ($cells[0] ?? '').'-'.($cells[3] ?? ''));
                    $sheetDest->setCellValue('B'.$filaNum, ($cells[1] ?? '').'-'.($cells[2] ?? ''));
                    $sheetDest->setCellValue('C'.$filaNum, $cells[4] ?? '');
                    $sheetDest->setCellValue('D'.$filaNum, $cells[5] ? ( str_contains($cells[5],'-') ? 0 :$cells[5]) : '');
                    $sheetDest->setCellValue('E'.$filaNum, $cells[6] ? ( str_contains($cells[6],'-') ? 0 :$cells[6]) : '');
                    $sheetDest->setCellValue('F'.$filaNum, $cells[7] ? ( str_contains($cells[7],'-') ? 0 :$cells[7]) : '');
                    $sheetDest->setCellValue('G'.$filaNum, '21');
                    $sheetDest->setCellValue('H'.$filaNum, '272');
                    $sheetDest->setCellValue('I'.$filaNum, '1');
                    $sheetDest->setCellValue('J'.$filaNum, '6');
                    $sheetDest->setCellValue('K'.$filaNum, '16');
                    $sheetDest->setCellValue('L'.$filaNum, '10');
                    $sheetDest->setCellValue('M'.$filaNum, '2026');

                    $filaNum++;
                }

                // liberar memoria por hoja
                $spreadsheetOrigen->disconnectWorksheets();
                unset($spreadsheetOrigen);
            }

            // 4️⃣ Guardar archivo
            Storage::disk('public')->makeDirectory('pruebas/files_up');

            $pathdestiny = Storage::disk('public')->path('pruebas/files_up/conceptos1.xlsx');
            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save($pathdestiny);

            Log::info('Archivo generado correctamente');

            return response()->json([
                'message' => 'listo',
                'filas_generadas' => $filaNum - 2
            ]);

        } catch (\Throwable $th) {
            Log::error($th);
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }
    public function TransformDataToImport2(Request $request)
    {
        ini_set('memory_limit', '1024M');
        set_time_limit(0);

        Log::info('entra');

        try {

            $pathorigin = Storage::disk('public')->path('pruebas/files/conceptos2.xlsx');

            // 1️⃣ Obtener nombres de hojas (NO carga el archivo completo)
            $reader = IOFactory::createReader('Xlsx');
            $sheetNames = $reader->listWorksheetNames($pathorigin);

            Log::info('Hojas detectadas: ' . count($sheetNames));

            // 2️⃣ Crear archivo destino
            $spreadsheet = new Spreadsheet();
            $sheetDest = $spreadsheet->getActiveSheet();

            // Encabezados
            $headers = [
                'NUM','TIPO','DESCRIPCION','MO','REFACCION','TOTAL',
                'CATEGORIA','SAT','UNIDAD','MODULO','CONTRATO','ZONA','ANIO'
            ];

            foreach ($headers as $i => $header) {
                $sheetDest->setCellValue(chr(65 + $i) . '1', $header);
            }

            $filaNum = 2;

            // 3️⃣ Procesar hoja por hoja
            $reader->setReadDataOnly(true);
            $hojanum=0;
            foreach ($sheetNames as $sheetName) {
                $hojanum++;
                Log::info("Procesando hoja: {$sheetName}");

                $reader->setLoadSheetsOnly([$sheetName]);
                $spreadsheetOrigen = $reader->load($pathorigin);
                $sheetOrigen = $spreadsheetOrigen->getActiveSheet();

                $index = 0;
                $tipo='';
                $invertir=false;

                foreach ($sheetOrigen->getRowIterator() as $row) {
                    $cells = [];
                    foreach ($row->getCellIterator() as $cell) {
                        $cells[] = trim((string) $cell->getFormattedValue());
                    }
                    if($hojanum === 136){
                        unset($cells[5]);
                    }
                    if ($index++ === 0) {   
                        if(isset($cells[6])){
                            $tipo=$cells[2];
                            $invertir=!str_contains($cells[4],'Mano'); 
                        }else if(isset($cells[5])){
                            $tipo=$cells[2];
                            $invertir=!str_contains($cells[3],'Mano'); 
                        }else{
                            $tipo=$cells[1];
                            LOG::info('Titulo');
                            LOG::info($cells[2]);
                            $invertir=!str_contains($cells[2],'Mano'); 
                        }
                        if($invertir){
                            LOG::info('checar hoja');
                        }
                        continue;
                    }

                    if (empty(array_filter($cells))) continue;
                    if(isset($cells[5])){
                        $num=($cells[0] ?? '').'-'.($cells[1] ?? '');
                        $des=$cells[2] ?? '';
                    }else{
                        $num=$cells[0]?? '';
                        $des=$cells[1]?? '';
                    }

                    if(isset($cells[6])){
                    $sat=$this->GetCatNum($cells[3],false);    
                    $cat=$this->GetCatNum($cells[3]);
                        $m=$invertir==true ? 5:4;
                        $r=$invertir==true ? 4:5;
                        $f=6;
                    }else if(isset($cells[5])){
                        $cat='21';
                        $m=$invertir==true ? 4:3;
                        $r=$invertir==true ? 3:4;
                        $f=5;
                    }else{
                        $cat='21';
                        $m=$invertir==true ? 3:2;
                        $r=$invertir==true ? 2:3;
                        $f=4;
                    }
                    


                    $sheetDest->setCellValue('A'.$filaNum, $num);
                    $sheetDest->setCellValue('B'.$filaNum, $tipo);
                    $sheetDest->setCellValue('C'.$filaNum, $des);
                    
                    $sheetDest->setCellValue('D'.$filaNum, $cells[$m] ? ( str_contains($cells[$m],'-') ? 0 :$cells[$m]) : '');
                    $sheetDest->setCellValue('E'.$filaNum, $cells[$r] ? ( str_contains($cells[$r],'-') ? 0 :$cells[$r]) : '');
                    $sheetDest->setCellValue('F'.$filaNum, $cells[$f] ? ( str_contains($cells[$f],'-') ? 0 :$cells[$f]) : '');
                    $sheetDest->setCellValue('G'.$filaNum, $cat);
                    $sheetDest->setCellValue('H'.$filaNum, $sat);
                    $sheetDest->setCellValue('I'.$filaNum, '1');
                    $sheetDest->setCellValue('J'.$filaNum, '6');
                    $sheetDest->setCellValue('K'.$filaNum, '17');
                    $sheetDest->setCellValue('L'.$filaNum, '10');
                    $sheetDest->setCellValue('M'.$filaNum, '2026');

                    $filaNum++;
                }

                // liberar memoria por hoja
                $spreadsheetOrigen->disconnectWorksheets();
                unset($spreadsheetOrigen);
            }

            // 4️⃣ Guardar archivo
            Storage::disk('public')->makeDirectory('pruebas/files_up');

            $pathdestiny = Storage::disk('public')->path('pruebas/files_up/conceptos2.xlsx');
            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save($pathdestiny);

            Log::info('Archivo generado correctamente');

            return response()->json([
                'message' => 'listo',
                'filas_generadas' => $filaNum - 2
            ]);

        } catch (\Throwable $th) {
            Log::error($th);
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }
    public function TransformDataToImport3(Request $request)
    {
        ini_set('memory_limit', '1024M');
        set_time_limit(0);

        Log::info('entra');

        try {

            $pathorigin = Storage::disk('public')->path('pruebas/files/conceptos3.xlsx');

            // 1️⃣ Obtener nombres de hojas (NO carga el archivo completo)
            $reader = IOFactory::createReader('Xlsx');
            $sheetNames = $reader->listWorksheetNames($pathorigin);

            Log::info('Hojas detectadas: ' . count($sheetNames));

            // 2️⃣ Crear archivo destino
            $spreadsheet = new Spreadsheet();
            $sheetDest = $spreadsheet->getActiveSheet();

            // Encabezados
            $headers = [
                'NUM','TIPO','DESCRIPCION','MO','REFACCION','TOTAL',
                'CATEGORIA','SAT','UNIDAD','MODULO','CONTRATO','ZONA','ANIO'
            ];

            foreach ($headers as $i => $header) {
                $sheetDest->setCellValue(chr(65 + $i) . '1', $header);
            }

            $filaNum = 2;

            // 3️⃣ Procesar hoja por hoja
            $reader->setReadDataOnly(true);
            $hojanum=0;
            foreach ($sheetNames as $sheetName) {
                $hojanum++;
                Log::info("Procesando hoja: {$sheetName}");

                $reader->setLoadSheetsOnly([$sheetName]);
                $spreadsheetOrigen = $reader->load($pathorigin);
                $sheetOrigen = $spreadsheetOrigen->getActiveSheet();

                $index = 0;
                $tipo='';
                foreach ($sheetOrigen->getRowIterator() as $row) {
                    $cells = [];
                    foreach ($row->getCellIterator() as $cell) {
                        $cells[] = trim((string) $cell->getFormattedValue());
                    }
                    
                    if ($index++ === 0) {   
                        $tipo=$cells[2];
                        continue;
                    }

                    if (empty(array_filter($cells))) continue;
                    $num=($cells[0] ?? '').'-'.($cells[1] ?? '');
                    $des=$cells[2] ?? '';
                    $cat='21';
                    $sat='272';
                    $m=3;
                    $r=4;
                    $f=5;
                    
                    

                    $sheetDest->setCellValue('A'.$filaNum, $num);
                    $sheetDest->setCellValue('B'.$filaNum, $tipo);
                    $sheetDest->setCellValue('C'.$filaNum, $des);
                    
                    $sheetDest->setCellValue('D'.$filaNum, $cells[$m] ? ( str_contains($cells[$m],'-') ? 0 :$cells[$m]) : '');
                    $sheetDest->setCellValue('E'.$filaNum, $cells[$r] ? ( str_contains($cells[$r],'-') ? 0 :$cells[$r]) : '');
                    $sheetDest->setCellValue('F'.$filaNum, $cells[$f] ? ( str_contains($cells[$f],'-') ? 0 :$cells[$f]) : '');
                    $sheetDest->setCellValue('G'.$filaNum, $cat);
                    $sheetDest->setCellValue('H'.$filaNum, $sat);
                    $sheetDest->setCellValue('I'.$filaNum, '1');
                    $sheetDest->setCellValue('J'.$filaNum, '6');
                    $sheetDest->setCellValue('K'.$filaNum, '15');
                    $sheetDest->setCellValue('L'.$filaNum, '10');
                    $sheetDest->setCellValue('M'.$filaNum, '2026');

                    $filaNum++;
                }
                
                // liberar memoria por hoja
                $spreadsheetOrigen->disconnectWorksheets();
                unset($spreadsheetOrigen);
            }

            // 4️⃣ Guardar archivo
            Storage::disk('public')->makeDirectory('pruebas/files_up');

            $pathdestiny = Storage::disk('public')->path('pruebas/files_up/conceptos3.xlsx');
            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save($pathdestiny);

            Log::info('Archivo generado correctamente');

            return response()->json([
                'message' => 'listo',
                'filas_generadas' => $filaNum - 2
            ]);

        } catch (\Throwable $th) {
            Log::error($th);
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }
    public function TransformDataToImport4(Request $request)
    {
        ini_set('memory_limit', '1024M');
        set_time_limit(0);

        Log::info('entra');

        try {

            $pathorigin = Storage::disk('public')->path('pruebas/files/conceptos4.xlsx');

            // 1️⃣ Obtener nombres de hojas (NO carga el archivo completo)
            $reader = IOFactory::createReader('Xlsx');
            $sheetNames = $reader->listWorksheetNames($pathorigin);

            Log::info('Hojas detectadas: ' . count($sheetNames));

            // 2️⃣ Crear archivo destino
            $spreadsheet = new Spreadsheet();
            $sheetDest = $spreadsheet->getActiveSheet();

            // Encabezados
            $headers = [
                'NUM','TIPO','DESCRIPCION','MO','REFACCION','TOTAL',
                'CATEGORIA','SAT','UNIDAD','MODULO','CONTRATO','ZONA','ANIO'
            ];

            foreach ($headers as $i => $header) {
                $sheetDest->setCellValue(chr(65 + $i) . '1', $header);
            }

            $filaNum = 2;

            // 3️⃣ Procesar hoja por hoja
            $reader->setReadDataOnly(true);
            $hojanum=0;
            foreach ($sheetNames as $sheetName) {
                $hojanum++;
                Log::info("Procesando hoja: {$sheetName}");

                $reader->setLoadSheetsOnly([$sheetName]);
                $spreadsheetOrigen = $reader->load($pathorigin);
                $sheetOrigen = $spreadsheetOrigen->getActiveSheet();

                $index = 0;
                $tipo='';
                foreach ($sheetOrigen->getRowIterator() as $row) {
                    $cells = [];
                    foreach ($row->getCellIterator() as $cell) {
                        $cells[] = trim((string) $cell->getFormattedValue());
                    }
                    
                    if ($index++ === 0) {   
                        $tipo=$cells[2];
                        continue;
                    }

                    if (empty(array_filter($cells))) continue;
                    $num=($cells[0] ?? '').'-'.($cells[1] ?? '');
                    $des=$cells[2] ?? '';
                    $cat='21';
                    $sat='272';
                    $m=4;
                    $r=3;
                    $f=5;
                    
                    

                    $sheetDest->setCellValue('A'.$filaNum, $num);
                    $sheetDest->setCellValue('B'.$filaNum, $tipo);
                    $sheetDest->setCellValue('C'.$filaNum, $des);
                    
                    $sheetDest->setCellValue('D'.$filaNum, $cells[$m] ? ( str_contains($cells[$m],'-') ? 0 :$cells[$m]) : '');
                    $sheetDest->setCellValue('E'.$filaNum, $cells[$r] ? ( str_contains($cells[$r],'-') ? 0 :$cells[$r]) : '');
                    $sheetDest->setCellValue('F'.$filaNum, $cells[$f] ? ( str_contains($cells[$f],'-') ? 0 :$cells[$f]) : '');
                    $sheetDest->setCellValue('G'.$filaNum, $cat);
                    $sheetDest->setCellValue('H'.$filaNum, $sat);
                    $sheetDest->setCellValue('I'.$filaNum, '1');
                    $sheetDest->setCellValue('J'.$filaNum, '6');
                    $sheetDest->setCellValue('K'.$filaNum, '20');
                    $sheetDest->setCellValue('L'.$filaNum, '10');
                    $sheetDest->setCellValue('M'.$filaNum, '2026');

                    $filaNum++;
                }
                
                // liberar memoria por hoja
                $spreadsheetOrigen->disconnectWorksheets();
                unset($spreadsheetOrigen);
            }

            // 4️⃣ Guardar archivo
            Storage::disk('public')->makeDirectory('pruebas/files_up');

            $pathdestiny = Storage::disk('public')->path('pruebas/files_up/conceptos4.xlsx');
            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save($pathdestiny);

            Log::info('Archivo generado correctamente');

            return response()->json([
                'message' => 'listo',
                'filas_generadas' => $filaNum - 2
            ]);

        } catch (\Throwable $th) {
            Log::error($th);
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }
    public function TransformDataToImport5(Request $request)
    {
        ini_set('memory_limit', '1024M');
        set_time_limit(0);
        try {
            $pathorigin = Storage::disk('public')->path('pruebas/files/conceptos5.xlsx');
            // 1️⃣ Obtener nombres de hojas (NO carga el archivo completo)
            $reader = IOFactory::createReader('Xlsx');
            $sheetNames = $reader->listWorksheetNames($pathorigin);

            Log::info('Hojas detectadas: ' . count($sheetNames));

            // 2️⃣ Crear archivo destino
            $spreadsheet = new Spreadsheet();
            $sheetDest = $spreadsheet->getActiveSheet();

            // Encabezados
            $headers = [
                'NUM','TIPO','DESCRIPCION','MO','REFACCION','TOTAL',
                'CATEGORIA','SAT','UNIDAD','MODULO','CONTRATO','ZONA','ANIO'
            ];

            foreach ($headers as $i => $header) {
                $sheetDest->setCellValue(chr(65 + $i) . '1', $header);
            }

            $filaNum = 2;

            // 3️⃣ Procesar hoja por hoja
            $reader->setReadDataOnly(true);
            $hojanum=0;
            foreach ($sheetNames as $sheetName) {
                $hojanum++;
                Log::info("Procesando hoja: {$sheetName}");

                $reader->setLoadSheetsOnly([$sheetName]);
                $spreadsheetOrigen = $reader->load($pathorigin);
                $sheetOrigen = $spreadsheetOrigen->getActiveSheet();

                $index = 0;
                $tipo='';
                foreach ($sheetOrigen->getRowIterator() as $row) {
                    $cells = [];
                    foreach ($row->getCellIterator() as $cell) {
                        $cells[] = trim((string) $cell->getFormattedValue());
                    }
                    
                    if ($index++ === 0) {   
                        $tipo=$cells[3];
                        continue;
                    }

                    if (empty(array_filter($cells))) continue;
                    $num=($cells[0] ?? '').'-'.($cells[1] ?? '');
                    $des=$cells[3] ?? '';
                    $cat='21';
                    $sat='272';
                    $m=$hojanum==1 ? 5:4;
                    $r=$m+1;
                    $f=$r+1;
                    
                    

                    $sheetDest->setCellValue('A'.$filaNum, $num);
                    $sheetDest->setCellValue('B'.$filaNum, $tipo);
                    $sheetDest->setCellValue('C'.$filaNum, $des);
                    
                    $sheetDest->setCellValue('D'.$filaNum, $cells[$m] ? ( str_contains($cells[$m],'-') ? 0 :$cells[$m]) : '');
                    $sheetDest->setCellValue('E'.$filaNum, $cells[$r] ? ( str_contains($cells[$r],'-') ? 0 :$cells[$r]) : '');
                    $sheetDest->setCellValue('F'.$filaNum, $cells[$f] ? ( str_contains($cells[$f],'-') ? 0 :$cells[$f]) : '');
                    $sheetDest->setCellValue('G'.$filaNum, $cat);
                    $sheetDest->setCellValue('H'.$filaNum, $sat);
                    $sheetDest->setCellValue('I'.$filaNum, '1');
                    $sheetDest->setCellValue('J'.$filaNum, '6');
                    $sheetDest->setCellValue('K'.$filaNum, '18');
                    $sheetDest->setCellValue('L'.$filaNum, '10');
                    $sheetDest->setCellValue('M'.$filaNum, '2026');

                    $filaNum++;
                }
                
                // liberar memoria por hoja
                $spreadsheetOrigen->disconnectWorksheets();
                unset($spreadsheetOrigen);
            }

            // 4️⃣ Guardar archivo
            Storage::disk('public')->makeDirectory('pruebas/files_up');

            $pathdestiny = Storage::disk('public')->path('pruebas/files_up/conceptos5.xlsx');
            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save($pathdestiny);

            Log::info('Archivo generado correctamente');

            return response()->json([
                'message' => 'listo',
                'filas_generadas' => $filaNum - 2
            ]);

        } catch (\Throwable $th) {
            Log::error($th);
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }
    public function TransformDataToImport6(Request $request)
    {
        ini_set('memory_limit', '1024M');
        set_time_limit(0);
        try {
            $pathorigin = Storage::disk('public')->path('pruebas/files/conceptos6.xlsx');
            // 1️⃣ Obtener nombres de hojas (NO carga el archivo completo)
            $reader = IOFactory::createReader('Xlsx');
            $sheetNames = $reader->listWorksheetNames($pathorigin);

            Log::info('Hojas detectadas: ' . count($sheetNames));

            // 2️⃣ Crear archivo destino
            $spreadsheet = new Spreadsheet();
            $sheetDest = $spreadsheet->getActiveSheet();

            // Encabezados
            $headers = [
                'NUM','TIPO','DESCRIPCION','MO','REFACCION','TOTAL',
                'CATEGORIA','SAT','UNIDAD','MODULO','CONTRATO','ZONA','ANIO'
            ];

            foreach ($headers as $i => $header) {
                $sheetDest->setCellValue(chr(65 + $i) . '1', $header);
            }

            $filaNum = 2;

            // 3️⃣ Procesar hoja por hoja
            $reader->setReadDataOnly(true);
            $hojanum=0;
            foreach ($sheetNames as $sheetName) {
                $hojanum++;
                Log::info("Procesando hoja: {$sheetName}");

                $reader->setLoadSheetsOnly([$sheetName]);
                $spreadsheetOrigen = $reader->load($pathorigin);
                $sheetOrigen = $spreadsheetOrigen->getActiveSheet();

                $index = 0;
                $tipo='';
                foreach ($sheetOrigen->getRowIterator() as $row) {
                    $cells = [];
                    foreach ($row->getCellIterator() as $cell) {
                        $cells[] = trim((string) $cell->getFormattedValue());
                    }
                    
                    if ($index++ === 0) {   
                        $tipo=$cells[3];
                        continue;
                    }

                    if (empty(array_filter($cells))) continue;
                    $num=($cells[0] ?? '').'-'.($cells[1] ?? '');
                    $des=$cells[3] ?? ' No registrada en los archivos';
                    $sat=$this->GetCatNum($cells[2],false);
                    $cat=$this->GetCatNum($cells[2]);
                    $m=5;
                    $r=4;
                    $f=6;
                    
                    

                    $sheetDest->setCellValue('A'.$filaNum, $num);
                    $sheetDest->setCellValue('B'.$filaNum, $tipo);
                    $sheetDest->setCellValue('C'.$filaNum, $des);
                    
                    $sheetDest->setCellValue('D'.$filaNum, $cells[$m] ? ( str_contains($cells[$m],'-') ? 0 :$cells[$m]) : '');
                    $sheetDest->setCellValue('E'.$filaNum, $cells[$r] ? ( str_contains($cells[$r],'-') ? 0 :$cells[$r]) : '');
                    $sheetDest->setCellValue('F'.$filaNum, $cells[$f] ? ( str_contains($cells[$f],'-') ? 0 :$cells[$f]) : '');
                    $sheetDest->setCellValue('G'.$filaNum, $cat);
                    $sheetDest->setCellValue('H'.$filaNum, $sat);
                    $sheetDest->setCellValue('I'.$filaNum, '1');
                    $sheetDest->setCellValue('J'.$filaNum, '6');
                    $sheetDest->setCellValue('K'.$filaNum, '19');
                    $sheetDest->setCellValue('L'.$filaNum, '10');
                    $sheetDest->setCellValue('M'.$filaNum, '2026');

                    $filaNum++;
                }
                
                // liberar memoria por hoja
                $spreadsheetOrigen->disconnectWorksheets();
                unset($spreadsheetOrigen);
            }

            // 4️⃣ Guardar archivo
            Storage::disk('public')->makeDirectory('pruebas/files_up');

            $pathdestiny = Storage::disk('public')->path('pruebas/files_up/conceptos6.xlsx');
            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save($pathdestiny);

            Log::info('Archivo generado correctamente');

            return response()->json([
                'message' => 'listo',
                'filas_generadas' => $filaNum - 2
            ]);

        } catch (\Throwable $th) {
            Log::error($th);
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }
    public function TransformDataToImport7(Request $request)
    {
        ini_set('memory_limit', '1024M');
        set_time_limit(0);
        try {
            $pathorigin = Storage::disk('public')->path('pruebas/files/conceptos7.xlsx');
            // 1️⃣ Obtener nombres de hojas (NO carga el archivo completo)
            $reader = IOFactory::createReader('Xlsx');
            $sheetNames = $reader->listWorksheetNames($pathorigin);

            Log::info('Hojas detectadas: ' . count($sheetNames));

            // 2️⃣ Crear archivo destino
            $spreadsheet = new Spreadsheet();
            $sheetDest = $spreadsheet->getActiveSheet();

            // Encabezados
            $headers = [
                'NUM','TIPO','DESCRIPCION','MO','REFACCION','TOTAL',
                'CATEGORIA','SAT','UNIDAD','MODULO','CONTRATO','ZONA','ANIO'
            ];

            foreach ($headers as $i => $header) {
                $sheetDest->setCellValue(chr(65 + $i) . '1', $header);
            }

            $filaNum = 2;

            // 3️⃣ Procesar hoja por hoja
            $reader->setReadDataOnly(true);
            $hojanum=0;
            foreach ($sheetNames as $sheetName) {
                $hojanum++;
                Log::info("Procesando hoja: {$sheetName}");

                $reader->setLoadSheetsOnly([$sheetName]);
                $spreadsheetOrigen = $reader->load($pathorigin);
                $sheetOrigen = $spreadsheetOrigen->getActiveSheet();

                $index = 0;
                $tipo='';
                foreach ($sheetOrigen->getRowIterator() as $row) {
                    $cells = [];
                    foreach ($row->getCellIterator() as $cell) {
                        $cells[] = trim((string) $cell->getFormattedValue());
                    }
                    
                    if ($index++ === 0) {   
                        $tipo=$cells[3];
                        continue;
                    }

                    if (empty(array_filter($cells))) continue;
                    $num=($cells[0] ?? '').'-'.($cells[1] ?? '');
                    $des=isset($cells[9]) ? $cells[3].' '.$cells[4] : $cells[3];
                    $sat=$this->GetCatNum($cells[2],false);
                    $cat=$this->GetCatNum($cells[2]);
                    $m=isset($cells[9]) ? 7 : (isset($cells[8]) ? 6 : 5);
                    $r=$m+1;
                    $f=$r+1;
                    
                    

                    $sheetDest->setCellValue('A'.$filaNum, $num);
                    $sheetDest->setCellValue('B'.$filaNum, $tipo);
                    $sheetDest->setCellValue('C'.$filaNum, $des);
                    
                    $sheetDest->setCellValue('D'.$filaNum, $cells[$m] ? ( str_contains($cells[$m],'-') ? 0 :$cells[$m]) : '');
                    $sheetDest->setCellValue('E'.$filaNum, $cells[$r] ? ( str_contains($cells[$r],'-') ? 0 :$cells[$r]) : '');
                    $sheetDest->setCellValue('F'.$filaNum, $cells[$f] ? ( str_contains($cells[$f],'-') ? 0 :$cells[$f]) : '');
                    $sheetDest->setCellValue('G'.$filaNum, $cat);
                    $sheetDest->setCellValue('H'.$filaNum, $sat);
                    $sheetDest->setCellValue('I'.$filaNum, '1');
                    $sheetDest->setCellValue('J'.$filaNum, '6');
                    $sheetDest->setCellValue('K'.$filaNum, '18');
                    $sheetDest->setCellValue('L'.$filaNum, '10');
                    $sheetDest->setCellValue('M'.$filaNum, '2026');

                    $filaNum++;
                }
                
                // liberar memoria por hoja
                $spreadsheetOrigen->disconnectWorksheets();
                unset($spreadsheetOrigen);
            }

            // 4️⃣ Guardar archivo
            Storage::disk('public')->makeDirectory('pruebas/files_up');

            $pathdestiny = Storage::disk('public')->path('pruebas/files_up/conceptos7.xlsx');
            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save($pathdestiny);

            Log::info('Archivo generado correctamente');

            return response()->json([
                'message' => 'listo',
                'filas_generadas' => $filaNum - 2
            ]);

        } catch (\Throwable $th) {
            Log::error($th);
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }
    public function TransformDataToImport8(Request $request)
    {
        ini_set('memory_limit', '1024M');
        set_time_limit(0);
        try {
            $pathorigin = Storage::disk('public')->path('pruebas/files/conceptos8.xlsx');
            // 1️⃣ Obtener nombres de hojas (NO carga el archivo completo)
            $reader = IOFactory::createReader('Xlsx');
            $sheetNames = $reader->listWorksheetNames($pathorigin);

            Log::info('Hojas detectadas: ' . count($sheetNames));

            // 2️⃣ Crear archivo destino
            $spreadsheet = new Spreadsheet();
            $sheetDest = $spreadsheet->getActiveSheet();

            // Encabezados
            $headers = [
                'NUM','TIPO','DESCRIPCION','MO','REFACCION','TOTAL',
                'CATEGORIA','SAT','UNIDAD','MODULO','CONTRATO','ZONA','ANIO'
            ];

            foreach ($headers as $i => $header) {
                $sheetDest->setCellValue(chr(65 + $i) . '1', $header);
            }

            $filaNum = 2;

            // 3️⃣ Procesar hoja por hoja
            $reader->setReadDataOnly(true);
            $hojanum=0;
            foreach ($sheetNames as $sheetName) {
                $hojanum++;
                Log::info("Procesando hoja: {$sheetName}");

                $reader->setLoadSheetsOnly([$sheetName]);
                $spreadsheetOrigen = $reader->load($pathorigin);
                $sheetOrigen = $spreadsheetOrigen->getActiveSheet();

                $index = 0;
                $tipo='';
                foreach ($sheetOrigen->getRowIterator() as $row) {
                    $cells = [];
                    foreach ($row->getCellIterator() as $cell) {
                        $cells[] = trim((string) $cell->getFormattedValue());
                    }
                    
                    if ($index++ === 0) {   
                        $tipo=$cells[3];
                        continue;
                    }

                    if (empty(array_filter($cells))) continue;
                    $num=($cells[0] ?? '').'-'.($cells[1] ?? '');
                    $des=$cells[3] ?? '';
                    $sat=$this->GetCatNum($cells[2],false);
                    $cat=$this->GetCatNum($cells[2]);
                    $m=4;
                    $r=$m+1;
                    $f=$r+1;
                    
                    

                    $sheetDest->setCellValue('A'.$filaNum, $num);
                    $sheetDest->setCellValue('B'.$filaNum, $tipo);
                    $sheetDest->setCellValue('C'.$filaNum, $des);
                    
                    $sheetDest->setCellValue('D'.$filaNum, $cells[$m] ? ( str_contains($cells[$m],'-') ? 0 :$cells[$m]) : '');
                    $sheetDest->setCellValue('E'.$filaNum, $cells[$r] ? ( str_contains($cells[$r],'-') ? 0 :$cells[$r]) : '');
                    $sheetDest->setCellValue('F'.$filaNum, $cells[$f] ? ( str_contains($cells[$f],'-') ? 0 :$cells[$f]) : '');
                    $sheetDest->setCellValue('G'.$filaNum, $cat);
                    $sheetDest->setCellValue('H'.$filaNum, $sat);
                    $sheetDest->setCellValue('I'.$filaNum, '1');
                    $sheetDest->setCellValue('J'.$filaNum, '6');
                    $sheetDest->setCellValue('K'.$filaNum, '18');
                    $sheetDest->setCellValue('L'.$filaNum, '10');
                    $sheetDest->setCellValue('M'.$filaNum, '2026');

                    $filaNum++;
                }
                
                // liberar memoria por hoja
                $spreadsheetOrigen->disconnectWorksheets();
                unset($spreadsheetOrigen);
            }

            // 4️⃣ Guardar archivo
            Storage::disk('public')->makeDirectory('pruebas/files_up');

            $pathdestiny = Storage::disk('public')->path('pruebas/files_up/conceptos8.xlsx');
            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save($pathdestiny);

            Log::info('Archivo generado correctamente');

            return response()->json([
                'message' => 'listo',
                'filas_generadas' => $filaNum - 2
            ]);

        } catch (\Throwable $th) {
            Log::error($th);
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }
    public function TransformDataToImport9(Request $request)
    {
        ini_set('memory_limit', '1024M');
        set_time_limit(0);
        try {
            $pathorigin = Storage::disk('public')->path('pruebas/files/conceptos9.xlsx');
            // 1️⃣ Obtener nombres de hojas (NO carga el archivo completo)
            $reader = IOFactory::createReader('Xlsx');
            $sheetNames = $reader->listWorksheetNames($pathorigin);

            Log::info('Hojas detectadas: ' . count($sheetNames));

            // 2️⃣ Crear archivo destino
            $spreadsheet = new Spreadsheet();
            $sheetDest = $spreadsheet->getActiveSheet();

            // Encabezados
            $headers = [
                'NUM','TIPO','DESCRIPCION','MO','REFACCION','TOTAL',
                'CATEGORIA','SAT','UNIDAD','MODULO','CONTRATO','ZONA','ANIO'
            ];

            foreach ($headers as $i => $header) {
                $sheetDest->setCellValue(chr(65 + $i) . '1', $header);
            }

            $filaNum = 2;

            // 3️⃣ Procesar hoja por hoja
            $reader->setReadDataOnly(true);
            $hojanum=0;
            foreach ($sheetNames as $sheetName) {
                $hojanum++;
                Log::info("Procesando hoja: {$sheetName}");

                $reader->setLoadSheetsOnly([$sheetName]);
                $spreadsheetOrigen = $reader->load($pathorigin);
                $sheetOrigen = $spreadsheetOrigen->getActiveSheet();

                $index = 0;
                $tipo='';
                foreach ($sheetOrigen->getRowIterator() as $row) {
                    $cells = [];
                    foreach ($row->getCellIterator() as $cell) {
                        $cells[] = trim((string) $cell->getFormattedValue());
                    }
                    
                    if ($index++ === 0) {   
                        $tipo=$cells[2];
                        continue;
                    }

                    if (empty(array_filter($cells))) continue;
                    $num=($cells[0] ?? '').'-'.($cells[1] ?? '');
                    $des=$cells[2] ?? '';
                    $cat='21';
                    $sat='272';
                    $m=3;
                    $r=$m+1;
                    $f=$r+1;
                    
                    

                    $sheetDest->setCellValue('A'.$filaNum, $num);
                    $sheetDest->setCellValue('B'.$filaNum, $tipo);
                    $sheetDest->setCellValue('C'.$filaNum, $des);
                    
                    $sheetDest->setCellValue('D'.$filaNum, $cells[$m] ? ( str_contains($cells[$m],'-') ? 0 :$cells[$m]) : '');
                    $sheetDest->setCellValue('E'.$filaNum, $cells[$r] ? ( str_contains($cells[$r],'-') ? 0 :$cells[$r]) : '');
                    $sheetDest->setCellValue('F'.$filaNum, $cells[$f] ? ( str_contains($cells[$f],'-') ? 0 :$cells[$f]) : '');
                    $sheetDest->setCellValue('G'.$filaNum, $cat);
                    $sheetDest->setCellValue('H'.$filaNum, $sat);
                    $sheetDest->setCellValue('I'.$filaNum, '1');
                    $sheetDest->setCellValue('J'.$filaNum, '6');
                    $sheetDest->setCellValue('K'.$filaNum, '21');
                    $sheetDest->setCellValue('L'.$filaNum, '10');
                    $sheetDest->setCellValue('M'.$filaNum, '2026');

                    $filaNum++;
                }
                
                // liberar memoria por hoja
                $spreadsheetOrigen->disconnectWorksheets();
                unset($spreadsheetOrigen);
            }

            // 4️⃣ Guardar archivo
            Storage::disk('public')->makeDirectory('pruebas/files_up');

            $pathdestiny = Storage::disk('public')->path('pruebas/files_up/conceptos9.xlsx');
            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save($pathdestiny);

            Log::info('Archivo generado correctamente');

            return response()->json([
                'message' => 'listo',
                'filas_generadas' => $filaNum - 2
            ]);

        } catch (\Throwable $th) {
            Log::error($th);
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }
    public function TransformDataToImport10(Request $request)
    {
        ini_set('memory_limit', '1024M');
        set_time_limit(0);
        try {
            $pathorigin = Storage::disk('public')->path('pruebas/files/conceptos10.xlsx');
            // 1️⃣ Obtener nombres de hojas (NO carga el archivo completo)
            $reader = IOFactory::createReader('Xlsx');
            $sheetNames = $reader->listWorksheetNames($pathorigin);

            Log::info('Hojas detectadas: ' . count($sheetNames));

            // 2️⃣ Crear archivo destino
            $spreadsheet = new Spreadsheet();
            $sheetDest = $spreadsheet->getActiveSheet();

            // Encabezados
            $headers = [
                'NUM','TIPO','DESCRIPCION','MO','REFACCION','TOTAL',
                'CATEGORIA','SAT','UNIDAD','MODULO','CONTRATO','ZONA','ANIO'
            ];

            foreach ($headers as $i => $header) {
                $sheetDest->setCellValue(chr(65 + $i) . '1', $header);
            }

            $filaNum = 2;

            // 3️⃣ Procesar hoja por hoja
            $reader->setReadDataOnly(true);
            $hojanum=0;
            foreach ($sheetNames as $sheetName) {
                $hojanum++;
                Log::info("Procesando hoja: {$sheetName}");

                $reader->setLoadSheetsOnly([$sheetName]);
                $spreadsheetOrigen = $reader->load($pathorigin);
                $sheetOrigen = $spreadsheetOrigen->getActiveSheet();

                $index = 0;
                $tipo='';
                foreach ($sheetOrigen->getRowIterator() as $row) {
                    $cells = [];
                    foreach ($row->getCellIterator() as $cell) {
                        $cells[] = trim((string) $cell->getFormattedValue());
                    }
                    
                    if (str_contains($cells[0],'nidad')) {   
                        $tipo=$cells[4] .' '.$cells[5];
                        continue;
                    }

                    if (empty(array_filter($cells))) continue;
                    $num=($cells[0] ?? '').'-'.($cells[1] ?? '');
                    $des=$cells[4] .' '.$cells[5];
                    $cat=$this->GetCatNum($cells[2]);
                    $sat=$this->GetCatNum($cells[2],false);
                    $m=7;
                    $r=9;
                    $f=11;
                    
                    

                    $sheetDest->setCellValue('A'.$filaNum, $num);
                    $sheetDest->setCellValue('B'.$filaNum, $tipo);
                    $sheetDest->setCellValue('C'.$filaNum, $des);
                    
                    $sheetDest->setCellValue('D'.$filaNum, $cells[$m] ? ( str_contains($cells[$m],'-') ? 0 :$cells[$m]) : '');
                    $sheetDest->setCellValue('E'.$filaNum, $cells[$r] ? ( str_contains($cells[$r],'-') ? 0 :$cells[$r]) : '');
                    $sheetDest->setCellValue('F'.$filaNum, $cells[$f] ? ( str_contains($cells[$f],'-') ? 0 :$cells[$f]) : '');
                    $sheetDest->setCellValue('G'.$filaNum, $cat);
                    $sheetDest->setCellValue('H'.$filaNum, $sat);
                    $sheetDest->setCellValue('I'.$filaNum, '1');
                    $sheetDest->setCellValue('J'.$filaNum, '6');
                    $sheetDest->setCellValue('K'.$filaNum, '21');
                    $sheetDest->setCellValue('L'.$filaNum, '10');
                    $sheetDest->setCellValue('M'.$filaNum, '2026');

                    $filaNum++;
                }
                
                // liberar memoria por hoja
                $spreadsheetOrigen->disconnectWorksheets();
                unset($spreadsheetOrigen);
            }

            // 4️⃣ Guardar archivo
            Storage::disk('public')->makeDirectory('pruebas/files_up');

            $pathdestiny = Storage::disk('public')->path('pruebas/files_up/conceptos10.xlsx');
            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save($pathdestiny);

            Log::info('Archivo generado correctamente');

            return response()->json([
                'message' => 'listo',
                'filas_generadas' => $filaNum - 2
            ]);

        } catch (\Throwable $th) {
            Log::error($th);
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }
    private function GetCatNum($cat,$tipo=true){
        $sat='324';
        if(str_contains($cat,'Eléctrico')){
            $cat='4';
            $sat='206';
        }
        if(str_contains($cat,'Aire Acon')){
            $cat='9';
            $sat='141';
        }
        if(str_contains($cat,'Alineación')|| str_contains($cat,'Balanceo')){
            $cat='22';
            $sat='324';
        }
        if(str_contains($cat,'Carrocer')){
            $cat='10';
            $sat='272';
            
        }
        if(str_contains($cat,'Diferencial')){
            $cat='5';
            $sat='186';

        }
        if(str_contains($cat,'Dirección')){
            $cat='6';
            $sat='51';
        }
        if(str_contains($cat,'Enfriamiento')){
            $cat='3';
            $sat='210';
        }
        if(str_contains($cat,'Escape')){
            $cat='8';
            $sat='177';
        }
        if(str_contains($cat,'Frenos')){
            $cat='2';
            $sat='37';
        }
        if(str_contains($cat,'Hojalater')){
            $cat='10';
            $sat='272';
        }
        if(str_contains($cat,'Llantas')  ){
            $cat='14';
            $sat='115';
        }
        if(str_contains($cat,'Preventivo')){
            $cat='11';
            $sat='324';
        }else if(str_contains($cat,'Mantenimiento')){
            $cat='12';
            $sat='324';
        }
        if(str_contains($cat,'Monitoreo')){
            $cat='21';
            $sat='272';
        }
        if(str_contains($cat,'Motor')){
            $cat='7';
            $sat='324';
        }
        if(str_contains($cat,'Muelles')){
            $cat='10';
            $sat='272';
        }
        if(str_contains($cat,'Multa')){
            $cat='10';
            $sat='272';
        }
        if(str_contains($cat,'Otros')){
            $cat='10';
            $sat='272';
        }
        if(str_contains($cat,'Pintura')){
            $cat='10';
            $sat='272';
        }
        if(str_contains($cat,'Sin Cla')){
            $cat='10';
            $sat='272';
        }
        if(str_contains($cat,'Suspensión')){
            $cat='6';
            $sat='51';
        }
        if(str_contains($cat,'Transmisión')){
            $cat='5';
            $sat='186';
        }
        if(str_contains($cat,'Sistema de Llenado')){
            $cat='21';
            $sat='272';
        }
        if(str_contains($cat,'Equipo Aliado')){
            $cat='10';
            $sat='272';
        }
        if(str_contains($cat,'Rescate vial')){
            $cat='10';
            $sat='272';
        }
        if(str_contains($cat,'Servicio de Grúa')){
            $cat='10';
            $sat='272';
        }
        if(str_contains($cat,'Sistema de gas')){
            $cat='10';
            $sat='272';
        }
        if(str_contains($cat,'Calefacción')){
            $cat='4';
            $sat='206';
        }
        if($tipo){
            return $cat;
        }
        return $sat;
    }
}

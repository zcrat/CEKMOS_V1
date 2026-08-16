<?php

namespace App\Console\Commands;

use App\Models\ValesAlmacen;
use App\Services\ValeAlmacenPdfService;
use Illuminate\Console\Command;

class GenerarValesAlmacenPdf extends Command
{
    protected $signature = 'vales-almacen:generar-pdfs
                            {--force : Regenerar también los archivos existentes}';

    protected $description = 'Generar y almacenar los PDF de los vales de almacén existentes';

    public function handle(ValeAlmacenPdfService $pdf): int
    {
        $query = ValesAlmacen::query()->with([
            'user',
            'conceptos.concepto',
            'orden_servicio.entrada',
            'orden_servicio.vehiculo.modelo.marca',
            'orden_servicio.vehiculo.modelo.motor',
        ]);
        $total = $query->count();
        $force = (bool) $this->option('force');
        $generados = 0;
        $omitidos = 0;
        $errores = 0;
        $barra = $this->output->createProgressBar($total);
        $barra->start();

        $query->chunkById(50, function ($vales) use ($pdf, $force, &$generados, &$omitidos, &$errores, $barra) {
            foreach ($vales as $vale) {
                try {
                    if (! $force && $pdf->exists($vale)) {
                        $omitidos++;
                    } else {
                        $pdf->generate($vale);
                        $generados++;
                    }
                } catch (\Throwable $exception) {
                    report($exception);
                    $errores++;
                } finally {
                    $barra->advance();
                }
            }
        });

        $barra->finish();
        $this->newLine(2);
        $this->info("Generados: {$generados}; omitidos: {$omitidos}; errores: {$errores}.");

        return $errores === 0 ? self::SUCCESS : self::FAILURE;
    }
}

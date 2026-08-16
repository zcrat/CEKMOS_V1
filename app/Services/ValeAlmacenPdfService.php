<?php

namespace App\Services;

use App\Models\ValesAlmacen;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Spatie\LaravelPdf\Facades\Pdf;

final class ValeAlmacenPdfService
{
    private const RENGLONES_BASE = 10;

    private const CARACTERES_POR_RENGLON = 70;

    private FilesystemAdapter $disk;

    private string $folder = 'vales-almacen';

    public function __construct()
    {
        $this->disk = Storage::disk('public');
    }

    public function generate(ValesAlmacen $vale): string
    {
        $filePath = "{$this->folder}/vale-{$vale->getKey()}.pdf";
        $this->disk->makeDirectory($this->folder);

        Pdf::view('pdf.ValeAlmacen', $this->dataPdf($vale))
            ->format('A4')
            ->landscape()
            ->save($this->disk->path($filePath));

        clearstatcache(true, $this->disk->path($filePath));

        if (! $this->disk->exists($filePath)) {
            throw new RuntimeException('No fue posible almacenar el PDF del vale de almacén.');
        }

        return $filePath;
    }

    public function delete(ValesAlmacen $vale): void
    {
        $this->disk->delete("{$this->folder}/vale-{$vale->getKey()}.pdf");
    }

    public function exists(ValesAlmacen $vale): bool
    {
        $absolutePath = $this->absolutePath($vale);

        return is_file($absolutePath) && filesize($absolutePath) > 0;
    }

    public function absolutePath(ValesAlmacen $vale): string
    {
        return $this->disk->path("{$this->folder}/vale-{$vale->getKey()}.pdf");
    }

    public function fileName(ValesAlmacen $vale): string
    {
        return "vale-almacen-{$this->folio($vale)}.pdf";
    }

    public function dataPdf(ValesAlmacen $vale): array
    {
        $vale->loadMissing([
            'user',
            'conceptos.concepto',
            'orden_servicio.entrada',
            'orden_servicio.vehiculo.modelo.marca',
            'orden_servicio.vehiculo.modelo.motor',
        ]);

        $orden = $vale->orden_servicio;
        $vehiculo = $orden->vehiculo;
        $modelo = $vehiculo?->modelo;
        $paginasConceptos = $this->paginarConceptos($vale->conceptos);

        return [
            'vale' => $vale,
            'folio' => $this->folio($vale),
            'orden' => $orden,
            'vehiculo' => $vehiculo,
            'descripcionVehiculo' => collect([
                $modelo?->marca?->descripcion,
                $modelo?->descripcion,
                $vehiculo?->{'año'},
                $vale->motor ?: $modelo?->motor?->descripcion,
                $vehiculo?->vin,
            ])->filter(fn ($valor) => filled($valor))->implode('-'),
            'paginasConceptos' => $paginasConceptos,
            'totalPaginas' => count($paginasConceptos),
            'kilometraje' => $orden->entrada?->kilometraje ?? '',
        ];
    }

    private function folio(ValesAlmacen $vale): string
    {
        return str_pad((string) $vale->getKey(), 5, '0', STR_PAD_LEFT).'-01';
    }

    private function paginarConceptos($conceptos): array
    {
        $paginas = [];
        $filas = [];
        $renglonesUsados = 0;

        foreach ($conceptos as $detalle) {
            $partes = $this->dividirDescripcionEnRenglones(
                (string) $detalle->concepto?->descripcion,
                self::CARACTERES_POR_RENGLON
            );
            $partes = $partes ?: [''];
            $primeraParte = true;

            while ($partes !== []) {
                if ($renglonesUsados === self::RENGLONES_BASE) {
                    $paginas[] = ['filas' => $filas, 'renglones_usados' => $renglonesUsados];
                    $filas = [];
                    $renglonesUsados = 0;
                }

                $disponibles = self::RENGLONES_BASE - $renglonesUsados;
                $lineas = array_splice($partes, 0, $disponibles);
                $unidades = count($lineas);

                $filas[] = [
                    'clave' => $primeraParte ? $detalle->concepto?->clave : 'CONT.',
                    'cantidad' => $primeraParte ? $detalle->cantidad : null,
                    'descripcion' => implode(' ', $lineas),
                    'unidades' => $unidades,
                ];
                $renglonesUsados += $unidades;
                $primeraParte = false;
            }
        }

        if ($filas !== [] || $paginas === []) {
            $paginas[] = ['filas' => $filas, 'renglones_usados' => $renglonesUsados];
        }

        return $paginas;
    }

    private function dividirDescripcionEnRenglones(string $descripcion, int $caracteresPorRenglon): array
    {
        if ($caracteresPorRenglon < 1) {
            throw new \InvalidArgumentException('Los caracteres por renglón deben ser mayores que cero.');
        }

        $descripcion = trim((string) preg_replace('/\s+/u', ' ', $descripcion));
        $palabras = preg_split('/\s+/u', $descripcion, -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $renglones = [];
        $renglon = '';

        foreach ($palabras as $palabra) {
            if (mb_strlen($palabra) > $caracteresPorRenglon) {
                if ($renglon !== '') {
                    $renglones[] = $renglon;
                    $renglon = '';
                }

                $fragmentos = mb_str_split($palabra, $caracteresPorRenglon);
                $renglon = array_pop($fragmentos) ?? '';
                array_push($renglones, ...$fragmentos);

                continue;
            }

            $candidato = $renglon === '' ? $palabra : "{$renglon} {$palabra}";
            if (mb_strlen($candidato) <= $caracteresPorRenglon) {
                $renglon = $candidato;

                continue;
            }

            $renglones[] = $renglon;
            $renglon = $palabra;
        }

        if ($renglon !== '') {
            $renglones[] = $renglon;
        }

        return $renglones ?: [''];
    }
}

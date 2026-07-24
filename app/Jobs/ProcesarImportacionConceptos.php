<?php

namespace App\Jobs;

use App\Events\ImportacionConceptosProgreso;
use App\Models\ArchivoSistema;
use App\Models\ConceptosPresupuestos;
use App\Models\CostosConceptosPresupuestos;
use App\Models\Marcas;
use App\Models\Modelos;
use App\Models\Motores;
use App\Models\VehiculosConceptos;
use App\Models\VehiculosConceptosDisponibles;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use InvalidArgumentException;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use RuntimeException;
use Throwable;

class ProcesarImportacionConceptos implements ShouldBeUnique, ShouldQueue
{
    use Queueable;

    public int $timeout = 900;

    public int $tries = 1;

    public int $uniqueFor = 1800;

    private const HEADERS = [
        'numero',
        'descripcion',
        'garantia_dias',
        'tipo_id',
        'modulo_id',
        'categoria_sat_id',
        'unidad_sat_id',
        'marca',
        'modelo',
        'motor',
        'anios',
        'p_refaccion',
        'p_mano_obra',
        'p_total',
    ];

    public function __construct(public int $archivoSistemaId) {}

    public function handle(): void
    {
        $archivo = ArchivoSistema::findOrFail($this->archivoSistemaId);
        if (in_array(
            $archivo->estatus_resultante,
            ['completado', 'completado_con_errores', 'eliminado'],
            true
        )) {
            return;
        }

        $spreadsheet = null;
        $currentRow = null;
        $totalRows = 0;
        $processed = 0;
        $imported = 0;
        $rowsWithErrors = 0;

        $archivo->update([
            'estatus_resultante' => 'procesando',
            'detalles_procesamiento' => [
                'resumen' => [
                    'total_filas' => 0,
                    'procesadas' => 0,
                    'importadas' => 0,
                    'con_error' => 0,
                ],
            ],
        ]);
        $this->publishProgress($archivo, 0, 0, 0, 0, 'procesando');

        try {
            $inputPath = Storage::disk($archivo->disco)->path($archivo->ruta_archivo);
            $spreadsheet = IOFactory::load($inputPath);
            $sheet = $spreadsheet->getSheetByName('Conceptos');

            if (! $sheet) {
                throw new RuntimeException('El archivo debe contener una hoja llamada "Conceptos".');
            }

            $headers = [];
            for ($column = 1; $column <= count(self::HEADERS); $column++) {
                $headers[] = trim((string) $sheet->getCell([$column, 4])->getValue());
            }

            if ($headers !== self::HEADERS) {
                throw new RuntimeException(
                    'Los encabezados de la hoja "Conceptos" fueron modificados. Descarga una plantilla nueva.'
                );
            }

            $sheet->setCellValue('O4', 'resultado_importacion');
            $sheet->getStyle('O4')->getFont()->setBold(true);

            $highestRow = min($sheet->getHighestDataRow(), 2004);
            $totalRows = $this->countRows($sheet, $highestRow);
            if ($totalRows === 0) {
                throw new RuntimeException('No se encontraron conceptos para importar.');
            }

            $errorGroups = [];

            DB::transaction(function () use (
                $archivo,
                $spreadsheet,
                $sheet,
                $highestRow,
                $totalRows,
                &$currentRow,
                &$processed,
                &$imported,
                &$rowsWithErrors,
                &$errorGroups
            ) {
                for ($rowNumber = 5; $rowNumber <= $highestRow; $rowNumber++) {
                    $currentRow = $rowNumber;
                    $values = $this->readRow($sheet, $rowNumber);

                    if ($this->isEmptyRow($values)) {
                        continue;
                    }

                    $rowErrors = [];
                    $values['garantia_dias'] = $values['garantia_dias'] === ''
                        ? null
                        : $values['garantia_dias'];
                    $values['marca'] = trim((string) ($values['marca'] ?? '')) ?: null;
                    $values['modelo'] = trim((string) ($values['modelo'] ?? '')) ?: null;

                    $validator = Validator::make(
                        $values,
                        $this->rules(),
                        $this->messages(),
                        $this->attributes()
                    );

                    if ($validator->fails()) {
                        $rowErrors = $validator->errors()->all();
                    } else {
                        try {
                            $data = $validator->validated();
                            $data['anios'] = $this->parseYears($data['anios']);

                            if ($data['marca'] === null) {
                                $data['marca'] = 'sin especificar';
                                $data['modelo'] = 'sin especificar';
                            }

                            $this->persistRow($data, $archivo);
                            $imported++;
                            $sheet->setCellValue("O{$rowNumber}", 'Importado');
                        } catch (InvalidArgumentException $exception) {
                            $rowErrors[] = $exception->getMessage();
                        }
                    }

                    if ($rowErrors !== []) {
                        $rowsWithErrors++;
                        $this->markRowWithErrors($sheet, $rowNumber, $rowErrors);
                        $this->groupErrors($errorGroups, $rowNumber, $rowErrors);
                    }

                    $processed++;
                    if ($processed % 10 === 0 || $processed === $totalRows) {
                        $this->publishProgress(
                            $archivo,
                            $totalRows,
                            $processed,
                            $imported,
                            $rowsWithErrors,
                            'procesando'
                        );
                    }
                }

                $details = $this->buildDetails(
                    $totalRows,
                    $processed,
                    $imported,
                    $rowsWithErrors,
                    $errorGroups
                );
                $status = $rowsWithErrors > 0 ? 'completado_con_errores' : 'completado';
                $resultPath = $this->saveResult($spreadsheet, $archivo);

                $archivo->update([
                    'estatus_resultante' => $status,
                    'ruta_resultado' => $resultPath,
                    'detalles_procesamiento' => $details,
                ]);
            });

            $this->publishProgress(
                $archivo,
                $totalRows,
                $processed,
                $imported,
                $rowsWithErrors,
                $rowsWithErrors > 0 ? 'completado_con_errores' : 'completado'
            );
        } catch (Throwable $exception) {
            if ($spreadsheet instanceof Spreadsheet && $currentRow !== null) {
                $sheet = $spreadsheet->getSheetByName('Conceptos');
                if ($sheet) {
                    $this->markRowWithErrors(
                        $sheet,
                        $currentRow,
                        ['Falla crítica: el procesamiento completo fue revertido.']
                    );
                }
            }

            $resultPath = $spreadsheet instanceof Spreadsheet
                ? rescue(fn () => $this->saveResult($spreadsheet, $archivo), null, false)
                : null;

            $archivo->update([
                'estatus_resultante' => 'fallido',
                'ruta_resultado' => $resultPath,
                'detalles_procesamiento' => [
                    'resumen' => [
                        'total_filas' => $totalRows,
                        'procesadas' => $processed,
                        'importadas' => 0,
                        'con_error' => $rowsWithErrors,
                    ],
                    'filas_importadas_antes_del_rollback' => $imported,
                    'falla_critica' => mb_substr($exception->getMessage(), 0, 500),
                    'rollback_completo' => true,
                ],
            ]);
            $this->publishProgress(
                $archivo,
                $totalRows,
                $processed,
                0,
                $rowsWithErrors,
                'fallido'
            );

            throw $exception;
        } finally {
            $spreadsheet?->disconnectWorksheets();
        }
    }

    private function readRow($sheet, int $rowNumber): array
    {
        $values = [];
        foreach (self::HEADERS as $column => $header) {
            $values[$header] = $sheet->getCell([$column + 1, $rowNumber])->getValue();
        }

        return $values;
    }

    private function isEmptyRow(array $values): bool
    {
        return trim((string) ($values['numero'] ?? '')) === ''
            && trim((string) ($values['descripcion'] ?? '')) === '';
    }

    private function countRows($sheet, int $highestRow): int
    {
        $total = 0;
        for ($row = 5; $row <= $highestRow; $row++) {
            if (! $this->isEmptyRow($this->readRow($sheet, $row))) {
                $total++;
            }
        }

        return $total;
    }

    private function persistRow(array $data, ArchivoSistema $archivo): void
    {
        $concepto = ConceptosPresupuestos::create([
            'num' => $data['numero'],
            'descripcion' => $data['descripcion'],
            'garantia_dias' => $data['garantia_dias'] ?? null,
            'fijo' => true,
            'tipo_id' => $data['tipo_id'],
            'modulo_orden_servicio_id' => $data['modulo_id'],
            'categoria_sat_id' => $data['categoria_sat_id'],
            'unidad_sat_id' => $data['unidad_sat_id'],
            'archivo_sistema_id' => $archivo->id,
        ]);

        $marca = Marcas::withTrashed()->firstOrCreate([
            'descripcion' => $this->normalizeDescription($data['marca']),
        ]);
        if ($marca->trashed()) {
            $marca->restore();
        }

        $motor = Motores::withTrashed()->firstOrCreate([
            'descripcion' => $this->normalizeDescription($data['motor']),
        ]);
        if ($motor->trashed()) {
            $motor->restore();
        }

        $modelo = Modelos::withTrashed()->firstOrCreate([
            'descripcion' => $this->normalizeDescription($data['modelo']),
            'marca_id' => $marca->id,
            'motor_id' => $motor->id,
        ]);
        if ($modelo->trashed()) {
            $modelo->restore();
        }

        $vehicleDescription = $this->vehicleDescription($marca, $modelo);
        $vehiculo = VehiculosConceptos::withTrashed()->firstOrCreate(
            ['modelo_id' => $modelo->id],
            ['descripcion' => $vehicleDescription, 'años' => []]
        );
        if ($vehiculo->trashed()) {
            $vehiculo->restore();
        }

        $years = collect($vehiculo->años ?? [])
            ->merge($data['anios'])
            ->map(fn ($year) => (int) $year)
            ->unique()
            ->sort()
            ->values()
            ->all();
        $vehiculo->update([
            'descripcion' => $vehicleDescription,
            'años' => $years,
        ]);

        $disponibilidad = VehiculosConceptosDisponibles::withTrashed()->firstOrCreate([
            'vehiculo_concepto_id' => $vehiculo->id,
            'modulo_orden_id' => $data['modulo_id'],
        ]);
        if ($disponibilidad->trashed()) {
            $disponibilidad->restore();
        }

        CostosConceptosPresupuestos::create([
            'concepto_presupuesto_id' => $concepto->id,
            'vehiculo_concepto_id' => $vehiculo->id,
            'usuario_id' => $archivo->usuario_id,
            'p_refaccion' => $data['p_refaccion'],
            'p_mano_obra' => $data['p_mano_obra'],
            'p_total' => $data['p_refaccion'] + $data['p_mano_obra'],
        ]);
    }

    private function markRowWithErrors($sheet, int $row, array $errors): void
    {
        $message = mb_substr(implode(' | ', array_unique($errors)), 0, 1000);
        $sheet->setCellValue("O{$row}", $message);
        $sheet->getStyle("A{$row}:O{$row}")->getFill()->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFFDE9E7');
        $sheet->getStyle("O{$row}")->getFont()->getColor()->setARGB('FFB91C1C');
        $sheet->getStyle("O{$row}")->getAlignment()->setWrapText(true);
    }

    private function groupErrors(array &$groups, int $row, array $errors): void
    {
        foreach (array_unique($errors) as $error) {
            $message = mb_substr((string) $error, 0, 250);
            $key = hash('sha256', $message);
            $groups[$key] ??= ['mensaje' => $message, 'filas' => [], 'cantidad' => 0];
            $groups[$key]['cantidad']++;

            if (count($groups[$key]['filas']) < 50) {
                $groups[$key]['filas'][] = $row;
            }
        }
    }

    private function buildDetails(
        int $total,
        int $processed,
        int $imported,
        int $rowsWithErrors,
        array $groups
    ): array {
        usort($groups, fn ($left, $right) => $right['cantidad'] <=> $left['cantidad']);
        $included = array_slice($groups, 0, 25);

        return [
            'resumen' => [
                'total_filas' => $total,
                'procesadas' => $processed,
                'importadas' => $imported,
                'con_error' => $rowsWithErrors,
            ],
            'errores_agrupados' => $included,
            'grupos_omitidos' => max(count($groups) - count($included), 0),
            'limites_detalle' => [
                'maximo_grupos' => 25,
                'maximo_filas_por_grupo' => 50,
            ],
            'rollback_completo' => false,
        ];
    }

    private function saveResult(Spreadsheet $spreadsheet, ArchivoSistema $archivo): string
    {
        $path = "importaciones/conceptos/resultados/{$archivo->id}.xlsx";
        Storage::disk($archivo->disco)->makeDirectory(dirname($path));
        (new Xlsx($spreadsheet))->save(Storage::disk($archivo->disco)->path($path));

        return $path;
    }

    private function publishProgress(
        ArchivoSistema $archivo,
        int $total,
        int $processed,
        int $imported,
        int $errors,
        string $status
    ): void {
        $progress = [
            'archivo_id' => $archivo->id,
            'estatus' => $status,
            'total_filas' => $total,
            'procesadas' => $processed,
            'importadas' => $imported,
            'con_error' => $errors,
            'porcentaje' => $total > 0 ? (int) floor(($processed / $total) * 100) : 0,
        ];

        rescue(
            fn () => Cache::store(config('imports.progress_cache_store', 'file'))->put(
                $this->cacheKey($archivo->id),
                $progress,
                now()->addHours(config('imports.progress_ttl_hours', 48))
            ),
            null,
            false
        );
        rescue(fn () => event(new ImportacionConceptosProgreso($archivo->usuario_id, $progress)), null, false);
    }

    public static function cacheKey(int $archivoId): string
    {
        return "importacion_conceptos:{$archivoId}:progreso";
    }

    public function uniqueId(): string
    {
        return (string) $this->archivoSistemaId;
    }

    private function rules(): array
    {
        return [
            'numero' => ['required', 'string', 'max:100'],
            'descripcion' => ['required', 'string', 'max:5000'],
            'garantia_dias' => ['nullable', 'integer', 'min:0', 'max:3650'],
            'tipo_id' => [
                'required',
                'integer',
                Rule::exists('tipos', 'id')->where('categoria_id', 7),
            ],
            'modulo_id' => ['required', 'integer', 'exists:modulo_ordenes_servicios,id'],
            'categoria_sat_id' => ['required', 'integer', 'exists:categorias_sat,id'],
            'unidad_sat_id' => ['required', 'integer', 'exists:unidades_sat,id'],
            'marca' => ['nullable', 'required_with:modelo', 'string', 'max:100'],
            'modelo' => ['nullable', 'required_with:marca', 'string', 'max:100'],
            'motor' => ['required', 'string', 'max:100'],
            'anios' => ['required'],
            'p_refaccion' => ['required', 'numeric', 'min:0', 'max:99999999.99'],
            'p_mano_obra' => ['required', 'numeric', 'min:0', 'max:99999999.99'],
            'p_total' => ['nullable'],
        ];
    }

    private function messages(): array
    {
        return [
            'marca.required_with' => 'La marca y el modelo deben capturarse juntos.',
            'modelo.required_with' => 'La marca y el modelo deben capturarse juntos.',
            'motor.required' => 'El motor es obligatorio.',
        ];
    }

    private function attributes(): array
    {
        return [
            'numero' => 'número',
            'descripcion' => 'descripción',
            'garantia_dias' => 'garantía en días',
            'tipo_id' => 'tipo',
            'modulo_id' => 'módulo',
            'categoria_sat_id' => 'categoría SAT',
            'unidad_sat_id' => 'unidad SAT',
            'marca' => 'marca',
            'modelo' => 'modelo',
            'motor' => 'motor',
            'anios' => 'años',
            'p_refaccion' => 'precio de refacción',
            'p_mano_obra' => 'precio de mano de obra',
        ];
    }

    private function parseYears(mixed $value): array
    {
        $expression = trim((string) $value);
        if ($expression === '') {
            throw new InvalidArgumentException('La columna años es obligatoria.');
        }

        $segments = preg_split('/\s*[,;]\s*/u', $expression, -1, PREG_SPLIT_NO_EMPTY);
        $years = [];
        $maximumYear = ((int) date('Y')) + 1;

        foreach ($segments ?: [] as $segment) {
            if (preg_match('/^\d{4}$/', $segment) === 1) {
                $start = (int) $segment;
                $end = $start;
            } elseif (preg_match('/^(\d{4})\s*[-–—]\s*(\d{4})$/u', $segment, $matches) === 1) {
                $start = (int) $matches[1];
                $end = (int) $matches[2];
            } else {
                throw new InvalidArgumentException(
                    "El valor de años \"{$expression}\" no es válido. Usa formatos como 2002, 2004 - 2008."
                );
            }

            if ($start < 1900 || $end > $maximumYear) {
                throw new InvalidArgumentException("Los años deben estar entre 1900 y {$maximumYear}.");
            }
            if ($start > $end) {
                throw new InvalidArgumentException(
                    "El rango {$start} - {$end} debe escribirse de menor a mayor."
                );
            }

            foreach (range($start, $end) as $year) {
                $years[$year] = $year;
            }
        }

        if ($years === []) {
            throw new InvalidArgumentException('No se encontraron años para importar.');
        }
        if (count($years) > 100) {
            throw new InvalidArgumentException('Una fila no puede incluir más de 100 años.');
        }

        ksort($years);

        return array_values($years);
    }

    private function normalizeDescription(string $description): string
    {
        return mb_strtolower(trim($description), 'UTF-8');
    }

    private function vehicleDescription(Marcas $brand, Modelos $model): string
    {
        if (
            $this->normalizeDescription($brand->descripcion) === 'sin especificar'
            && $this->normalizeDescription($model->descripcion) === 'sin especificar'
        ) {
            $motor = $model->motor()->withTrashed()->value('descripcion') ?? 'sin especificar';

            return "Todos Los Modelos de {$motor}";
        }

        return trim("{$brand->descripcion} {$model->descripcion}");
    }
}

<?php

namespace App\Http\Controllers;

use App\Jobs\ProcesarImportacionConceptos;
use App\Models\ArchivoSistema;
use App\Models\CategoriasSat;
use App\Models\ConceptosPresupuestos;
use App\Models\CostosConceptosPresupuestos;
use App\Models\Marcas;
use App\Models\Modelos;
use App\Models\ModuloOrdenesServicio;
use App\Models\Motores;
use App\Models\Tipos;
use App\Models\UnidadesSat;
use App\Models\VehiculosConceptos;
use App\Models\VehiculosConceptosDisponibles;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\NamedRange;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class ImportacionConceptosController extends Controller
{
    public function catalogos(): JsonResponse
    {
        return response()->json($this->catalogosDisponibles());
    }

    public function plantilla(): StreamedResponse
    {
        $catalogos = $this->catalogosDisponibles();
        $spreadsheet = $this->buildTemplate($catalogos);

        return response()->streamDownload(
            function () use ($spreadsheet) {
                (new Xlsx($spreadsheet))->save('php://output');
                $spreadsheet->disconnectWorksheets();
            },
            'plantilla_importacion_conceptos.xlsx',
            [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Cache-Control' => 'no-store, no-cache',
            ]
        );
    }

    public function encolarImportacion(Request $request): JsonResponse
    {
        $request->validate([
            'archivo' => ['required', 'file', 'mimes:xlsx,xls', 'max:10240'],
            'datos_entrada' => ['nullable', 'array'],
        ]);

        $archivoSubido = $request->file('archivo');
        $extension = strtolower($archivoSubido->getClientOriginalExtension());
        $disk = 'local';
        $path = $archivoSubido->storeAs(
            'importaciones/conceptos/originales',
            Str::uuid().".{$extension}",
            $disk
        );

        if (! $path) {
            return response()->json([
                'message' => 'No fue posible almacenar el archivo para procesarlo.',
            ], 500);
        }

        try {
            $archivoSistema = ArchivoSistema::create([
                'nombre_archivo' => $archivoSubido->getClientOriginalName(),
                'tipo_archivo' => $extension,
                'disco' => $disk,
                'ruta_archivo' => $path,
                'usuario_id' => Auth::id(),
                'estatus_resultante' => 'pendiente',
                'datos_entrada' => array_merge($request->input('datos_entrada', []), [
                    'usuario' => Auth::user()?->name ?: (Auth::user()?->usuario ?? ''),
                    'mime' => $archivoSubido->getClientMimeType(),
                    'tamano_bytes' => $archivoSubido->getSize(),
                ]),
                'detalles_procesamiento' => [
                    'resumen' => [
                        'total_filas' => 0,
                        'procesadas' => 0,
                        'importadas' => 0,
                        'con_error' => 0,
                    ],
                ],
            ]);

            ProcesarImportacionConceptos::dispatch($archivoSistema->id);
        } catch (Throwable $exception) {
            Storage::disk($disk)->delete($path);
            throw $exception;
        }

        return response()->json([
            'message' => 'El archivo fue enviado a procesamiento.',
            'archivo_sistema_id' => $archivoSistema->id,
            'estatus' => 'pendiente',
        ], 202);
    }

    public function importar(Request $request): JsonResponse
    {
        $request->validate([
            'archivo' => ['required', 'file', 'mimes:xlsx,xls', 'max:10240'],
            'datos_entrada' => ['nullable', 'array'],
        ]);

        $archivoSubido = $request->file('archivo');
        $archivoSistema = ArchivoSistema::create([
            'nombre_archivo' => $archivoSubido->getClientOriginalName(),
            'tipo_archivo' => strtolower($archivoSubido->getClientOriginalExtension()),
            'usuario_id' => Auth::id(),
            'estatus_resultante' => 'procesando',
            'datos_entrada' => array_merge($request->input('datos_entrada', []), [
                'usuario' => Auth::user()?->name ?: (Auth::user()?->usuario ?? ''),
                'mime' => $archivoSubido->getClientMimeType(),
                'tamano_bytes' => $archivoSubido->getSize(),
            ]),
        ]);

        try {
            $spreadsheet = IOFactory::load($archivoSubido->getRealPath());
            $sheet = $spreadsheet->getSheetByName('Conceptos');

            if (! $sheet) {
                throw ValidationException::withMessages([
                    'archivo' => ['El archivo debe contener una hoja llamada "Conceptos".'],
                ]);
            }

            $expectedHeaders = [
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

            $headers = [];
            for ($column = 1; $column <= count($expectedHeaders); $column++) {
                $headers[] = trim((string) $sheet->getCell([$column, 4])->getValue());
            }

            if ($headers !== $expectedHeaders) {
                throw ValidationException::withMessages([
                    'archivo' => [
                        'Los encabezados de la hoja "Conceptos" fueron modificados. Descarga una plantilla nueva.',
                    ],
                ]);
            }

            $rows = [];
            $errors = [];
            $highestRow = min($sheet->getHighestDataRow(), 2004);

            for ($rowNumber = 5; $rowNumber <= $highestRow; $rowNumber++) {
                $values = [];
                foreach ($expectedHeaders as $column => $header) {
                    $values[$header] = $sheet->getCell([$column + 1, $rowNumber])->getValue();
                }

                if (
                    trim((string) ($values['numero'] ?? '')) === ''
                    && trim((string) ($values['descripcion'] ?? '')) === ''
                ) {
                    continue;
                }

                $values['garantia_dias'] = $values['garantia_dias'] === '' ? null : $values['garantia_dias'];
                $values['marca'] = trim((string) ($values['marca'] ?? '')) ?: null;
                $values['modelo'] = trim((string) ($values['modelo'] ?? '')) ?: null;

                $validator = Validator::make(
                    $values,
                    $this->conceptoRules((int) ($values['modulo_id'] ?? 0), true),
                    $this->conceptoMessages(),
                    $this->conceptoAttributes()
                );

                if ($validator->fails()) {
                    foreach ($validator->errors()->all() as $error) {
                        $errors[] = "Fila {$rowNumber}: {$error}";
                    }

                    continue;
                }

                try {
                    $validatedRow = $validator->validated();
                    $validatedRow['anios'] = $this->parseYears($validatedRow['anios']);
                    $rowMode = $validatedRow['marca'] === null ? 'global' : 'especifico';

                    if ($rowMode === 'global') {
                        $validatedRow['marca'] = 'sin especificar';
                        $validatedRow['modelo'] = 'sin especificar';
                    }

                    $rows[] = $validatedRow;
                } catch (InvalidArgumentException $exception) {
                    $errors[] = "Fila {$rowNumber}: {$exception->getMessage()}";
                }
            }

            $spreadsheet->disconnectWorksheets();

            if ($errors !== []) {
                throw ValidationException::withMessages([
                    'archivo' => array_slice($errors, 0, 100),
                ]);
            }

            if ($rows === []) {
                throw ValidationException::withMessages([
                    'archivo' => ['No se encontraron conceptos para importar.'],
                ]);
            }

            $asignaciones = DB::transaction(function () use ($rows, $archivoSistema) {
                $asignaciones = 0;

                foreach ($rows as $data) {
                    $concepto = ConceptosPresupuestos::create([
                        'num' => $data['numero'],
                        'descripcion' => $data['descripcion'],
                        'garantia_dias' => $data['garantia_dias'] ?? null,
                        'fijo' => true,
                        'tipo_id' => $data['tipo_id'],
                        'modulo_orden_servicio_id' => $data['modulo_id'],
                        'categoria_sat_id' => $data['categoria_sat_id'],
                        'unidad_sat_id' => $data['unidad_sat_id'],
                        'archivo_sistema_id' => $archivoSistema->id,
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

                    $modelDescription = $this->normalizeDescription($data['modelo']);
                    $modelo = Modelos::withTrashed()->firstOrCreate([
                        'descripcion' => $modelDescription,
                        'marca_id' => $marca->id,
                        'motor_id' => $motor->id,
                    ]);
                    if ($modelo->trashed()) {
                        $modelo->restore();
                    }

                    $vehicleDescription = $this->vehicleDescription($marca, $modelo);
                    $vehiculo = VehiculosConceptos::withTrashed()->firstOrCreate(
                        ['modelo_id' => $modelo->id],
                        [
                            'descripcion' => $vehicleDescription,
                            'años' => [],
                        ]
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

                    CostosConceptosPresupuestos::updateOrCreate(
                        [
                            'concepto_presupuesto_id' => $concepto->id,
                            'vehiculo_concepto_id' => $vehiculo->id,
                        ],
                        [
                            'usuario_id' => Auth::id(),
                            'p_refaccion' => $data['p_refaccion'],
                            'p_mano_obra' => $data['p_mano_obra'],
                            'p_total' => $data['p_refaccion'] + $data['p_mano_obra'],
                        ]
                    );
                    $asignaciones++;
                }

                return $asignaciones;
            });

            $archivoSistema->update([
                'estatus_resultante' => 'completado',
                'datos_entrada' => array_merge($archivoSistema->datos_entrada ?? [], [
                    'conceptos_importados' => count($rows),
                    'asignaciones_creadas' => $asignaciones,
                ]),
            ]);

            return response()->json([
                'message' => count($rows)." conceptos importados en {$asignaciones} vehículos.",
                'importados' => count($rows),
                'asignaciones' => $asignaciones,
                'archivo_sistema_id' => $archivoSistema->id,
            ]);
        } catch (Throwable $exception) {
            $archivoSistema->update([
                'estatus_resultante' => 'error',
                'datos_entrada' => array_merge($archivoSistema->datos_entrada ?? [], [
                    'error' => $exception->getMessage(),
                ]),
            ]);

            throw $exception;
        }
    }

    public function importaciones(): JsonResponse
    {
        $items = ArchivoSistema::query()
            ->with('usuario:id,name,usuario')
            ->withCount('conceptos_presupuestos')
            ->whereIn('tipo_archivo', ['xlsx', 'xls'])
            ->orderByDesc('id')
            ->limit(50)
            ->get()
            ->map(function (ArchivoSistema $archivo) {
                $progress = Cache::store(config('imports.progress_cache_store', 'file'))->get(
                    ProcesarImportacionConceptos::cacheKey($archivo->id),
                    $this->progressFromDatabase($archivo)
                );

                return [
                    'id' => $archivo->id,
                    'nombre_archivo' => $archivo->nombre_archivo,
                    'tipo_archivo' => $archivo->tipo_archivo,
                    'usuario' => $archivo->usuario?->name
                        ?: ($archivo->usuario?->usuario ?? ($archivo->datos_entrada['usuario'] ?? '')),
                    'estatus_resultante' => $archivo->estatus_resultante,
                    'conceptos' => $archivo->conceptos_presupuestos_count,
                    'fecha' => $archivo->created_at?->format('d/m/Y H:i'),
                    'progreso' => $progress,
                    'detalles_procesamiento' => $archivo->detalles_procesamiento,
                    'tiene_resultado' => filled($archivo->ruta_resultado),
                    'puede_eliminar' => in_array(
                        $archivo->estatus_resultante,
                        ['completado', 'completado_con_errores'],
                        true
                    ) && $archivo->conceptos_presupuestos_count > 0,
                ];
            })
            ->values();

        return response()->json(compact('items'));
    }

    public function progresoImportacion(ArchivoSistema $archivoSistema): JsonResponse
    {
        $progress = Cache::store(config('imports.progress_cache_store', 'file'))->get(
            ProcesarImportacionConceptos::cacheKey($archivoSistema->id),
            $this->progressFromDatabase($archivoSistema)
        );

        return response()->json([
            'progreso' => $progress,
            'detalles_procesamiento' => $archivoSistema->detalles_procesamiento,
            'tiene_resultado' => filled($archivoSistema->ruta_resultado),
        ]);
    }

    public function descargarResultado(ArchivoSistema $archivoSistema): StreamedResponse
    {
        abort_unless(
            filled($archivoSistema->ruta_resultado)
                && Storage::disk($archivoSistema->disco)->exists($archivoSistema->ruta_resultado),
            404
        );

        $name = pathinfo($archivoSistema->nombre_archivo, PATHINFO_FILENAME)
            .'_resultado.xlsx';

        return Storage::disk($archivoSistema->disco)->download(
            $archivoSistema->ruta_resultado,
            $name
        );
    }

    public function destroyImportacion(ArchivoSistema $archivoSistema): JsonResponse
    {
        $conceptosAsignados = $archivoSistema->conceptos_presupuestos()
            ->whereHas('presupuestos_asignados')
            ->count();

        if ($conceptosAsignados > 0) {
            throw ValidationException::withMessages([
                'archivo' => [
                    "No se puede eliminar la importación porque {$conceptosAsignados} conceptos ya se usan en presupuestos.",
                ],
            ]);
        }

        $eliminados = DB::transaction(function () use ($archivoSistema) {
            $conceptos = $archivoSistema->conceptos_presupuestos()->count();
            $archivoSistema->conceptos_presupuestos()->delete();
            $archivoSistema->update([
                'estatus_resultante' => 'eliminado',
                'datos_entrada' => array_merge($archivoSistema->datos_entrada ?? [], [
                    'eliminacion' => [
                        'usuario_id' => Auth::id(),
                        'fecha' => now()->toIso8601String(),
                        'conceptos_eliminados' => $conceptos,
                    ],
                ]),
            ]);

            return $conceptos;
        });

        return response()->json([
            'message' => "{$eliminados} conceptos de la importación fueron eliminados.",
            'eliminados' => $eliminados,
        ]);
    }

    private function progressFromDatabase(ArchivoSistema $archivo): array
    {
        $summary = $archivo->detalles_procesamiento['resumen'] ?? [];
        $total = (int) ($summary['total_filas'] ?? 0);
        $processed = (int) ($summary['procesadas'] ?? 0);

        return [
            'archivo_id' => $archivo->id,
            'estatus' => $archivo->estatus_resultante,
            'total_filas' => $total,
            'procesadas' => $processed,
            'importadas' => (int) ($summary['importadas'] ?? 0),
            'con_error' => (int) ($summary['con_error'] ?? 0),
            'porcentaje' => $total > 0 ? (int) floor(($processed / $total) * 100) : 0,
        ];
    }

    private function validateConcepto(Request $request): array
    {
        return $request->validate(
            $this->conceptoRules($request->integer('modulo_id')),
            $this->conceptoMessages(),
            $this->conceptoAttributes()
        );
    }

    private function conceptoRules(?int $moduloId = null, bool $importing = false): array
    {
        $rules = [
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
            'p_refaccion' => ['required', 'numeric', 'min:0', 'max:99999999.99'],
            'p_mano_obra' => ['required', 'numeric', 'min:0', 'max:99999999.99'],
            'p_total' => ['nullable'],
        ];

        if ($importing) {
            $rules['marca'] = ['nullable', 'required_with:modelo', 'string', 'max:100'];
            $rules['modelo'] = ['nullable', 'required_with:marca', 'string', 'max:100'];
            $rules['motor'] = ['required', 'string', 'max:100'];
            $rules['anios'] = ['required'];
        } else {
            $rules['vehiculo_id'] = [
                'required',
                'integer',
                Rule::exists('vehiculos_conceptos_disponibles', 'vehiculo_concepto_id')
                    ->where(
                        fn ($query) => $query
                            ->where('modulo_orden_id', $moduloId)
                            ->whereNull('deleted_at')
                    ),
            ];
        }

        return $rules;
    }

    private function conceptoMessages(): array
    {
        return [
            'marca.required_with' => 'La marca y el modelo deben capturarse juntos.',
            'modelo.required_with' => 'La marca y el modelo deben capturarse juntos.',
            'motor.required' => 'El motor es obligatorio.',
        ];
    }

    private function conceptoAttributes(): array
    {
        return [
            'numero' => 'número',
            'descripcion' => 'descripción',
            'garantia_dias' => 'garantía en días',
            'tipo_id' => 'tipo',
            'modulo_id' => 'módulo',
            'categoria_sat_id' => 'categoría SAT',
            'unidad_sat_id' => 'unidad SAT',
            'vehiculo_id' => 'vehículo',
            'marca' => 'marca',
            'modelo' => 'modelo',
            'motor' => 'motor',
            'anios' => 'años',
            'p_refaccion' => 'precio de refacción',
            'p_mano_obra' => 'precio de mano de obra',
        ];
    }

    /**
     * @return array<int, int>
     */
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
                throw new InvalidArgumentException(
                    "Los años deben estar entre 1900 y {$maximumYear}."
                );
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
        $brandDescription = $this->normalizeDescription($brand->descripcion);
        $modelDescription = $this->normalizeDescription($model->descripcion);

        if (
            $brandDescription === 'sin especificar'
            && $modelDescription === 'sin especificar'
        ) {
            $motor = $model->motor()->withTrashed()->value('descripcion') ?? 'sin especificar';

            return "Todos Los Modelos de {$motor}";
        }

        return trim("{$brand->descripcion} {$model->descripcion}");
    }

    private function catalogosDisponibles(): array
    {
        return [
            'tipos' => Tipos::query()
                ->where('categoria_id', 7)
                ->orderBy('descripcion')
                ->get(['id', 'descripcion']),
            'modulos' => ModuloOrdenesServicio::query()
                ->orderBy('descripcion')
                ->get(['id', 'descripcion']),
            'categorias_sat' => CategoriasSat::query()
                ->orderBy('descripcion')
                ->get(['id', 'descripcion', 'codigo_sat']),
            'unidades_sat' => UnidadesSat::query()
                ->orderBy('descripcion')
                ->get(['id', 'descripcion', 'codigo']),
            'vehiculos' => VehiculosConceptosDisponibles::query()
                ->with([
                    'vehiculo_concepto:id,descripcion',
                    'modulo_ordenes_servicio:id,descripcion',
                ])
                ->orderBy('modulo_orden_id')
                ->orderBy('vehiculo_concepto_id')
                ->get()
                ->map(fn (VehiculosConceptosDisponibles $item) => (object) [
                    'id' => $item->vehiculo_concepto_id,
                    'descripcion' => $item->vehiculo_concepto?->descripcion ?? '',
                    'modulo_id' => $item->modulo_orden_id,
                    'modulo' => $item->modulo_ordenes_servicio?->descripcion ?? '',
                ]),
            'marcas_modelos' => Modelos::query()
                ->with([
                    'marca:id,descripcion',
                    'motor:id,descripcion',
                    'vehiculos_conceptos:id,modelo_id,años',
                ])
                ->orderBy('marca_id')
                ->orderBy('descripcion')
                ->get()
                ->map(fn (Modelos $item) => (object) [
                    'marca' => $item->marca?->descripcion ?? '',
                    'modelo' => $item->descripcion,
                    'motor' => $item->motor?->descripcion ?? '',
                    'anios' => $item->vehiculos_conceptos
                        ->flatMap(fn (VehiculosConceptos $vehicle) => $vehicle->años ?? [])
                        ->unique()
                        ->sort()
                        ->implode(', '),
                ])
                ->unique(fn ($item) => mb_strtolower($item->marca.'|'.$item->modelo.'|'.$item->motor))
                ->values(),
        ];
    }

    private function buildTemplate(array $catalogos): Spreadsheet
    {
        $spreadsheet = new Spreadsheet;
        $conceptosSheet = $spreadsheet->getActiveSheet();
        $conceptosSheet->setTitle('Conceptos');
        $catalogosSheet = $spreadsheet->createSheet();
        $catalogosSheet->setTitle('Catalogos');

        $conceptosSheet->mergeCells('A1:N1');
        $conceptosSheet->setCellValue('A1', 'Plantilla de importación de conceptos');
        $conceptosSheet->mergeCells('A2:N2');
        $conceptosSheet->setCellValue(
            'A2',
            'Desde la fila 5: marca y modelo deben llenarse ambos o dejarse ambos vacíos por fila. Motor obligatorio. Años: 2002, 2004 - 2008.'
        );

        $headers = [
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
        $conceptosSheet->fromArray($headers, null, 'A4');

        for ($row = 5; $row <= 204; $row++) {
            $conceptosSheet->setCellValue(
                "N{$row}",
                "=IF(OR(L{$row}=\"\",M{$row}=\"\"),\"\",L{$row}+M{$row})"
            );
        }

        $catalogosSheet->mergeCells('A1:R1');
        $catalogosSheet->setCellValue('A1', 'Catálogos disponibles');
        $catalogosSheet->fromArray(
            [
                'Tipo ID',
                'Tipo',
                '',
                'Módulo ID',
                'Módulo',
                '',
                'Categoría SAT ID',
                'Categoría SAT',
                'Código SAT',
                '',
                'Unidad SAT ID',
                'Unidad SAT',
                'Código',
                '',
                'Marca existente',
                'Modelo existente',
                'Motor existente',
                'Años registrados',
            ],
            null,
            'A3'
        );

        foreach ($catalogos['tipos'] as $index => $item) {
            $row = $index + 4;
            $catalogosSheet->fromArray([$item->id, $item->descripcion], null, "A{$row}");
        }
        foreach ($catalogos['modulos'] as $index => $item) {
            $row = $index + 4;
            $catalogosSheet->fromArray([$item->id, $item->descripcion], null, "D{$row}");
        }
        foreach ($catalogos['categorias_sat'] as $index => $item) {
            $row = $index + 4;
            $catalogosSheet->fromArray(
                [$item->id, $item->descripcion, $item->codigo_sat],
                null,
                "G{$row}"
            );
        }
        foreach ($catalogos['unidades_sat'] as $index => $item) {
            $row = $index + 4;
            $catalogosSheet->fromArray(
                [$item->id, $item->descripcion, $item->codigo],
                null,
                "K{$row}"
            );
        }
        foreach ($catalogos['marcas_modelos'] as $index => $item) {
            $row = $index + 4;
            $catalogosSheet->fromArray(
                [$item->marca, $item->modelo, $item->motor, $item->anios],
                null,
                "O{$row}"
            );
        }

        $validationRanges = [
            'D' => ['TiposDisponibles', 'A', max(4, count($catalogos['tipos']) + 3)],
            'E' => ['ModulosDisponibles', 'D', max(4, count($catalogos['modulos']) + 3)],
            'F' => ['CategoriasSatDisponibles', 'G', max(4, count($catalogos['categorias_sat']) + 3)],
            'G' => ['UnidadesSatDisponibles', 'K', max(4, count($catalogos['unidades_sat']) + 3)],
        ];

        foreach ($validationRanges as $conceptColumn => [$rangeName, $catalogColumn, $lastRow]) {
            $spreadsheet->addNamedRange(
                new NamedRange(
                    $rangeName,
                    $catalogosSheet,
                    "\${$catalogColumn}\$4:\${$catalogColumn}\${$lastRow}"
                )
            );

            for ($row = 5; $row <= 204; $row++) {
                $this->addListValidation(
                    $conceptosSheet,
                    "{$conceptColumn}{$row}",
                    $rangeName
                );
            }
        }

        $titleStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 16],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '176CB3']],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
        ];
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1F4E78']],
            'borders' => [
                'bottom' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '17365D']],
            ],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
        ];

        $conceptosSheet->getStyle('A1:N1')->applyFromArray($titleStyle);
        $conceptosSheet->getStyle('A4:N4')->applyFromArray($headerStyle);
        $catalogosSheet->getStyle('A1:R1')->applyFromArray($titleStyle);
        $catalogosSheet->getStyle('A3:R3')->applyFromArray($headerStyle);
        $conceptosSheet->getStyle('A2:N2')->getFont()->setItalic(true)->setColor(
            new \PhpOffice\PhpSpreadsheet\Style\Color('666666')
        );

        $conceptosSheet->getStyle('L5:N204')->getNumberFormat()->setFormatCode('$#,##0.00');
        $conceptosSheet->getStyle('A5:A204')->getNumberFormat()->setFormatCode('@');
        $conceptosSheet->getStyle('K5:K204')->getNumberFormat()->setFormatCode('@');
        $conceptosSheet->freezePane('A5');
        $catalogosSheet->freezePane('A4');
        $conceptosSheet->setAutoFilter('A4:N204');

        $widths = [
            'A' => 18,
            'B' => 42,
            'C' => 15,
            'D' => 12,
            'E' => 12,
            'F' => 12,
            'G' => 18,
            'H' => 18,
            'I' => 20,
            'J' => 20,
            'K' => 22,
            'L' => 18,
            'M' => 20,
            'N' => 18,
        ];
        foreach ($widths as $column => $width) {
            $conceptosSheet->getColumnDimension($column)->setWidth($width);
        }

        foreach (range('A', 'R') as $column) {
            $catalogosSheet->getColumnDimension($column)->setAutoSize(true);
        }

        foreach (['C', 'F', 'J', 'N'] as $separator) {
            $catalogosSheet->getColumnDimension($separator)->setWidth(3);
        }

        $conceptosSheet->setShowGridlines(false);
        $catalogosSheet->setShowGridlines(false);
        $spreadsheet->setActiveSheetIndex(0);

        return $spreadsheet;
    }

    private function addListValidation(
        \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet,
        string $cell,
        string $formula
    ): void {
        $validation = $sheet->getCell($cell)->getDataValidation();
        $validation->setType(DataValidation::TYPE_LIST);
        $validation->setErrorStyle(DataValidation::STYLE_STOP);
        $validation->setAllowBlank(false);
        $validation->setShowDropDown(true);
        $validation->setShowErrorMessage(true);
        $validation->setErrorTitle('Valor no válido');
        $validation->setError('Seleccione un valor disponible en la hoja Catalogos.');
        $validation->setFormula1($formula);
    }
}

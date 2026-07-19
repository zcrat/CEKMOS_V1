<?php

namespace App\Http\Controllers;

use App\Models\AfinacionMotorInspeccionVehicular;
use App\Models\BandasInspeccionVehicular;
use App\Models\ElectricoInspeccionVehicular;
use App\Models\EscapeInspeccionVehicular;
use App\Models\FiltrosInspeccionVehicular;
use App\Models\FrenosInspeccionVehicular;
use App\Models\InspeccionVehicular;
use App\Models\LiquidosInspeccionVehicular;
use App\Models\LLantasInspeccionVehicular;
use App\Models\LucesEspiasInspeccionVehicular;
use App\Models\ManguerasInspeccionVehicular;
use App\Models\OrdenesServicio;
use App\Models\SeguridadInspeccionVehicular;
use App\Models\SuspencionDireccionInspeccionVehicular;
use App\Models\TrenTransmisionInspeccionVehicular;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class InspeccionVehicularController extends Controller
{
    private const STATUS_IDS = [23, 24, 25];

    private const SECTIONS = [
        'luces_espias' => [
            'model' => LucesEspiasInspeccionVehicular::class,
            'status' => ['codigo'],
            'notes' => true,
        ],
        'liquidos' => [
            'model' => LiquidosInspeccionVehicular::class,
            'status' => [
                'alternador_aire_acondicionado', 'transmision',
                'diferencial_frente_trasero', 'refrigerante', 'frenos',
                'direccion_hidraulica', 'limpiaparabrisas',
            ],
            'boolean' => [
                'alternador_aire_acondicionado_ok', 'alternador_aire_acondicionado_lleno',
                'transmision_ok', 'transmision_lleno',
                'diferencial_frente_trasero_ok', 'diferencial_frente_trasero_lleno',
                'refrigerante_ok', 'refrigerante_lleno',
                'frenos_ok', 'frenos_lleno',
                'direccion_hidraulica_ok', 'direccion_hidraulica_lleno',
                'limpiaparabrisas_ok', 'limpiaparabrisas_lleno',
            ],
            'notes' => true,
        ],
        'mangueras' => [
            'model' => ManguerasInspeccionVehicular::class,
            'status' => ['refrigerante', 'direccion_aire_acondicionado', 'calefaccion'],
        ],
        'bandas' => [
            'model' => BandasInspeccionVehicular::class,
            'status' => ['accesorios', 'bandas_direccion_hidraulica', 'alternador_aire_acondicionado'],
        ],
        'filtros' => [
            'model' => FiltrosInspeccionVehicular::class,
            'status' => ['aire', 'combustible', 'aceite'],
            'notes' => true,
        ],
        'llantas' => [
            'model' => LLantasInspeccionVehicular::class,
            'status' => [
                'izquierda_delantera', 'izquierda_trasera', 'derecha_delantera',
                'derecha_trasera', 'refaccion', 'alineacion_balanceo',
            ],
            'numeric' => [
                'izquierda_delantera_presion', 'izquierda_trasera_presion',
                'derecha_delantera_presion', 'derecha_trasera_presion',
                'refaccion_presion',
            ],
        ],
        'seguridad' => [
            'model' => SeguridadInspeccionVehicular::class,
            'status' => [
                'frenos_emergencia', 'limpiaparabrisas_izquierdo_derecho',
                'limpiaparabrisas_trasero',
            ],
            'notes' => true,
        ],
        'suspencion_direccion' => [
            'model' => SuspencionDireccionInspeccionVehicular::class,
            'status' => ['amortiguadores_suspencion', 'juntas_direccion_rotulas'],
            'notes' => true,
        ],
        'tren_transmision' => [
            'model' => TrenTransmisionInspeccionVehicular::class,
            'status' => [
                'filtro_transmison', 'union_transmision_clutch',
                'eje_traccion_juntas_homocineticas',
                'eje_transmision_juntas_universales', 'rodamientos_rueda',
                'tren_transmision', 'clutch',
            ],
            'notes' => true,
        ],
        'electrico' => [
            'model' => ElectricoInspeccionVehicular::class,
            'status' => [
                'sistema_carga_bateria', 'cables_conexiones_fusibles',
                'faro_izquierda', 'faro_derecha', 'cuarto_izquierda',
                'cuarto_derecha', 'reversa_frenos',
                'direccionales_izquierda_delantera',
                'direccionales_derecha_delantera',
                'direccionales_izquierda_trasera',
                'direccionales_derecha_trasera', 'intermitentes',
            ],
        ],
        'afinacion_motor' => [
            'model' => AfinacionMotorInspeccionVehicular::class,
            'status' => ['tapa_distribuidor_bujias_cables', 'fuel_injection'],
        ],
        'frenos' => [
            'model' => FrenosInspeccionVehicular::class,
            'status' => [
                'pastillas_izquierda_delantera', 'pastillas_izquierda_trasera',
                'pastillas_derecha_delantera', 'pastillas_derecha_trasera',
                'rotores_izquierda_delantera', 'rotores_izquierda_trasera',
                'rotores_derecha_delantera', 'rotores_derecha_trasera',
                'pinzas_cilindros_rueda_izquierda_delantera',
                'pinzas_cilindros_rueda_izquierda_trasera',
                'pinzas_cilindros_rueda_derecha_delantera',
                'pinzas_cilindros_rueda_derecha_trasera',
            ],
        ],
        'escape' => [
            'model' => EscapeInspeccionVehicular::class,
            'status' => ['mofle_convertidor_catlitico', 'sensores_soporte_tubos'],
            'notes' => true,
        ],
    ];

    public function read(OrdenesServicio $ordenServicio): JsonResponse
    {
        $inspeccion = InspeccionVehicular::with(array_keys(self::SECTIONS))
            ->where('orden_servicio_id', $ordenServicio->id)
            ->first();

        if (! $inspeccion) {
            return response()->json(['exists' => false, 'data' => null]);
        }

        $data = [];
        foreach (self::SECTIONS as $section => $definition) {
            $fields = $this->fieldsFor($definition);
            $data[$section] = $inspeccion->{$section}?->only($fields) ?? [];
        }

        return response()->json([
            'exists' => true,
            'data' => $data,
        ]);
    }

    public function save(Request $request): JsonResponse
    {
        $validated = $request->validate($this->rules());

        [$inspeccion, $created] = DB::transaction(function () use ($validated, $request) {
            $inspeccion = InspeccionVehicular::with(array_keys(self::SECTIONS))
                ->where('orden_servicio_id', $validated['orden_servicio_id'])
                ->lockForUpdate()
                ->first();
            $created = ! $inspeccion;
            $foreignKeys = [];

            foreach (self::SECTIONS as $section => $definition) {
                $sectionData = Arr::only($validated[$section], $this->fieldsFor($definition));
                if ($definition['notes'] ?? false) {
                    $sectionData['notas'] = $sectionData['notas'] ?? '';
                }

                $sectionModel = $inspeccion?->{$section};
                if ($sectionModel) {
                    $sectionModel->update($sectionData);
                } else {
                    $modelClass = $definition['model'];
                    $sectionModel = $modelClass::create($sectionData);
                }

                $foreignKeys[$section.'_id'] = $sectionModel->id;
            }

            $parentData = [
                ...$foreignKeys,
                'user_id' => $request->user()->id,
            ];

            if ($inspeccion) {
                $inspeccion->update($parentData);
            } else {
                $inspeccion = InspeccionVehicular::create([
                    ...$parentData,
                    'orden_servicio_id' => $validated['orden_servicio_id'],
                ]);
            }

            return [$inspeccion, $created];
        });

        return response()->json([
            'message' => $created
                ? 'Inspección vehicular creada correctamente.'
                : 'Inspección vehicular actualizada correctamente.',
            'id' => $inspeccion->id,
            'created' => $created,
        ], $created ? 201 : 200);
    }

    private function rules(): array
    {
        $rules = [
            'orden_servicio_id' => ['required', 'integer', 'exists:ordenes_servicio,id'],
        ];

        foreach (self::SECTIONS as $section => $definition) {
            $rules[$section] = ['required', 'array'];

            foreach ($definition['status'] as $field) {
                $rules[$section.'.'.$field] = [
                    'required',
                    'integer',
                    Rule::in(self::STATUS_IDS),
                ];
            }

            foreach ($definition['boolean'] ?? [] as $field) {
                $rules[$section.'.'.$field] = ['present', 'boolean'];
            }

            foreach ($definition['numeric'] ?? [] as $field) {
                $rules[$section.'.'.$field] = ['required', 'numeric', 'min:0', 'max:999.99'];
            }

            if ($definition['notes'] ?? false) {
                $rules[$section.'.notas'] = ['nullable', 'string', 'max:500'];
            }
        }

        return $rules;
    }

    private function fieldsFor(array $definition): array
    {
        return [
            ...$definition['status'],
            ...($definition['boolean'] ?? []),
            ...($definition['numeric'] ?? []),
            ...(($definition['notes'] ?? false) ? ['notas'] : []),
        ];
    }
}

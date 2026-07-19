<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="{{ asset('css/pdf.css') }}">
</head>
<body class="a4 contedor_bordes flex overflow-hidden inspeccion-pdf">
    @php
        if (!function_exists('celdaInspeccionVehicular')) {
            function celdaInspeccionVehicular($encabezado, $texto, $ancho = 100)
            {
                return '<div class="celda1 w-'.$ancho.'"><p class="texto-celda"><span class="encabezado-celda">'
                    .e($encabezado).': </span>'.e($texto ?? '').'</p></div>';
            }
        }

        if (!function_exists('marcadoresInspeccionVehicular')) {
            function marcadoresInspeccionVehicular($valor, array $estatus): string
            {
                $estatusId = is_numeric($valor) ? (int) $valor : null;
                $estatusActual = $estatusId !== null ? ($estatus[$estatusId] ?? null) : null;
                $seleccion = $estatusActual['tipo'] ?? null;

                $clase = static fn ($tipo) => $seleccion && $seleccion !== $tipo
                    ? ' inspection-marker--muted'
                    : ($seleccion === $tipo ? ' inspection-marker--selected' : '');

                $palomita = static fn ($tipo) => $seleccion === $tipo
                    ? '<span class="inspection-marker-check">&#10003;</span>'
                    : '';

                return '<span class="inspection-markers" title="'.e($estatusActual['descripcion'] ?? '').'">'
                    .'<i class="inspection-square'.$clase('inmediata').'">'.$palomita('inmediata').'</i>'
                    .'<i class="inspection-triangle'.$clase('futura').'">'.$palomita('futura').'</i>'
                    .'<i class="inspection-circle'.$clase('bien').'">'.$palomita('bien').'</i>'
                    .'</span>';
            }
        }

        if (!function_exists('casillaInspeccionVehicular')) {
            function casillaInspeccionVehicular($valor): string
            {
                return '<i class="inspection-checkbox'.($valor ? ' inspection-checkbox--checked' : '').'">'
                    .($valor ? '&#10003;' : '').'</i>';
            }
        }

        $estatusInspeccion = $estatus_inspeccion ?? [];
        $lucesEspias = $inspeccion?->luces_espias;
        $liquidos = $inspeccion?->liquidos;
        $mangueras = $inspeccion?->mangueras;
        $bandas = $inspeccion?->bandas;
        $filtros = $inspeccion?->filtros;
        $llantas = $inspeccion?->llantas;
        $seguridad = $inspeccion?->seguridad;
        $suspension = $inspeccion?->suspencion_direccion;
        $tren = $inspeccion?->tren_transmision;
        $electrico = $inspeccion?->electrico;
        $afinacion = $inspeccion?->afinacion_motor;
        $frenos = $inspeccion?->frenos;
        $escape = $inspeccion?->escape;

        $columnas26 = [
            [
                'ancho' => '34',
                'secciones' => [
                    [
                        'titulo' => 'REVISIÓN DE LUCES ESPÍAS',
                        'tipo' => 'normal',
                        'filas' => [
                            ['etiqueta' => 'Código(s):', 'valor' => $lucesEspias?->codigo],
                        ],
                        'notas' =>$lucesEspias?->notas,
                    ],
                    [
                        'titulo' => 'LÍQUIDOS',
                        'tipo' => 'liquidos',
                        'filas' => [
                            ['etiqueta' => 'Aceite de motor:', 'valor' => $liquidos?->aceite_motor ?? $liquidos?->alternador_aire_acondicionado, 'ok' => $liquidos?->aceite_motor_ok ?? $liquidos?->alternador_aire_acondicionado_ok, 'lleno' => $liquidos?->aceite_motor_lleno ?? $liquidos?->alternador_aire_acondicionado_lleno],
                            ['etiqueta' => 'Transmisión:', 'valor' => $liquidos?->transmision, 'ok' => $liquidos?->transmision_ok, 'lleno' => $liquidos?->transmision_lleno],
                            ['etiqueta' => 'Diferencial frente/trasero:', 'valor' => $liquidos?->diferencial_frente_trasero, 'ok' => $liquidos?->diferencial_frente_trasero_ok, 'lleno' => $liquidos?->diferencial_frente_trasero_lleno],
                            ['etiqueta' => 'Refrigerante:', 'valor' => $liquidos?->refrigerante, 'ok' => $liquidos?->refrigerante_ok, 'lleno' => $liquidos?->refrigerante_lleno],
                            ['etiqueta' => 'Frenos:', 'valor' => $liquidos?->frenos, 'ok' => $liquidos?->frenos_ok, 'lleno' => $liquidos?->frenos_lleno],
                            ['etiqueta' => 'Dirección hidráulica:', 'valor' => $liquidos?->direccion_hidraulica, 'ok' => $liquidos?->direccion_hidraulica_ok, 'lleno' => $liquidos?->direccion_hidraulica_lleno],
                            ['etiqueta' => 'Limpiaparabrisas:', 'valor' => $liquidos?->limpiaparabrisas, 'ok' => $liquidos?->limpiaparabrisas_ok, 'lleno' => $liquidos?->limpiaparabrisas_lleno],
                        ],
                        'notas' => $liquidos?->notas,
                    ],
                ],
            ],
            [
                'ancho' => '26',
                'secciones' => [
                    [
                        'titulo' => 'MANGUERAS',
                        'tipo' => 'normal',
                        'filas' => [
                            ['etiqueta' => 'Refrigerante:', 'valor' => $mangueras?->refrigerante],
                            ['etiqueta' => 'Dirección/Aire acondic.:', 'valor' => $mangueras?->direccion_aire_acondicionado],
                            ['etiqueta' => 'Calefacción:', 'valor' => $mangueras?->calefaccion],
                        ],
                    ],
                    [
                        'titulo' => 'BANDAS',
                        'tipo' => 'normal',
                        'filas' => [
                            ['etiqueta' => 'Accesorios:', 'valor' => $bandas?->accesorios],
                            ['etiqueta' => 'Dirección hidráulica:', 'valor' => $bandas?->bandas_direccion_hidraulica],
                            ['etiqueta' => 'Alternador/A. acondic.:', 'valor' => $bandas?->alternador_aire_acondicionado],
                        ],
                    ],
                    [
                        'titulo' => 'FILTROS',
                        'tipo' => 'normal',
                        'filas' => [
                            ['etiqueta' => 'Aire:', 'valor' => $filtros?->aire],
                            ['etiqueta' => 'Combustible:', 'valor' => $filtros?->combustible],
                            ['etiqueta' => 'Aceite:', 'valor' => $filtros?->aceite],
                        ],
                        'notas' => $filtros?->notas,
                    ],
                ],
            ],
            [
                'ancho' => '40',
                'secciones' => [
                    [
                        'titulo' => 'LLANTAS',
                        'tipo' => 'llantas',
                        'filas' => [
                            ['etiqueta' => 'I. delantera:', 'valor' => $llantas?->izquierda_delantera, 'presion' => $llantas?->izquierda_delantera_presion],
                            ['etiqueta' => 'I. trasera:', 'valor' => $llantas?->izquierda_trasera, 'presion' => $llantas?->izquierda_trasera_presion],
                            ['etiqueta' => 'D. delantera:', 'valor' => $llantas?->derecha_delantera, 'presion' => $llantas?->derecha_delantera_presion],
                            ['etiqueta' => 'D. trasera:', 'valor' => $llantas?->derecha_trasera, 'presion' => $llantas?->derecha_trasera_presion],
                            ['etiqueta' => 'Refacción:', 'valor' => $llantas?->refaccion, 'presion' => $llantas?->refaccion_presion],
                        ],
                    ],
                    [
                        'titulo' => 'EL DESGASTE DE NEUMÁTICOS INDICA QUE:',
                        'tipo' => 'normal',
                        'compacta' => true,
                        'filas' => [
                            ['etiqueta' => 'Se necesita alineación y balanceo:', 'valor' => $llantas?->alineacion_balanceo],
                        ],
                    ],
                    [
                        'titulo' => 'SEGURIDAD',
                        'tipo' => 'normal',
                        'filas' => [
                            ['etiqueta' => 'Freno de emergencia:', 'valor' => $seguridad?->frenos_emergencia],
                        ],
                    ],
                    [
                        'titulo' => 'LIMPIAPARABRISAS',
                        'tipo' => 'pares',
                        'filas' => [
                            [
                                ['etiqueta' => 'Izq./Der.:', 'valor' => $seguridad?->limpiaparabrisas_izquierdo_derecho],
                                ['etiqueta' => 'Trasero:', 'valor' => $seguridad?->limpiaparabrisas_trasero],
                            ],
                        ],
                        'notas' => $seguridad?->notas,
                    ],
                ],
            ],
        ];

        $columnas57 = [
            [
                'ancho' => '35',
                'secciones' => [
                    [
                        'titulo' => 'SUSPENSIÓN/DIRECCIÓN',
                        'tipo' => 'normal',
                        'filas' => [
                            ['etiqueta' => 'Amortiguadores/Suspensión:', 'valor' => $suspension?->amortiguadores_suspencion],
                            ['etiqueta' => 'Juntas de dirección/Rótulas:', 'valor' => $suspension?->juntas_direccion_rotulas],
                        ],
                        'notas' => $suspension?->notas,
                    ],
                    [
                        'titulo' => 'TREN DE TRANSMISIÓN',
                        'tipo' => 'normal',
                        'filas' => [
                            ['etiqueta' => 'Filtro de transmisión:', 'valor' => $tren?->filtro_transmison],
                            ['etiqueta' => 'Unión de transmisión/Clutch:', 'valor' => $tren?->union_transmision_clutch],
                            ['etiqueta' => 'Eje de tracción y juntas homocinéticas:', 'valor' => $tren?->eje_traccion_juntas_homocineticas],
                            ['etiqueta' => 'Eje de transmisión y juntas universales:', 'valor' => $tren?->eje_transmision_juntas_universales],
                            ['etiqueta' => 'Rodamientos de rueda:', 'valor' => $tren?->rodamientos_rueda],
                        ],
                    ],
                    [
                        'titulo' => '',
                        'tipo' => 'pares',
                        'filas' => [
                            [
                                ['etiqueta' => 'Transmisión:', 'valor' => $tren?->tren_transmision],
                                ['etiqueta' => 'Clutch:', 'valor' => $tren?->clutch],
                            ],
                        ],
                        'notas' => $tren?->notas,
                    ],
                ],
            ],
            [
                'ancho' => '32',
                'secciones' => [
                    [
                        'titulo' => 'ELÉCTRICO',
                        'tipo' => 'normal',
                        'filas' => [
                            ['etiqueta' => 'Sistema de carga/Batería:', 'valor' => $electrico?->sistema_carga_bateria],
                            ['etiqueta' => 'Cables/Conexiones/Fusibles:', 'valor' => $electrico?->cables_conexiones_fusibles],
                        ],
                    ],
                    [
                        'titulo' => 'LUCES',
                        'tipo' => 'normal',
                        'subtitulo' => true,
                        'filas' => [
                            ['etiqueta' => 'Freno/Reversa:', 'valor' => $electrico?->reversa_frenos],
                            ['etiqueta' => 'Intermitentes:', 'valor' => $electrico?->intermitentes],
                        ],
                    ],
                    [
                        'titulo' => '',
                        'tipo' => 'pares',
                        'encabezados' => ['FAROS', 'CUARTOS'],
                        'filas' => [
                            [
                                ['etiqueta' => 'IZQ.:', 'valor' => $electrico?->faro_izquierda],
                                ['etiqueta' => 'IZQ.:', 'valor' => $electrico?->cuarto_izquierda],
                            ],
                            [
                                ['etiqueta' => 'DER.:', 'valor' => $electrico?->faro_derecha],
                                ['etiqueta' => 'DER.:', 'valor' => $electrico?->cuarto_derecha],
                            ],
                        ],
                    ],
                    [
                        'titulo' => 'DIRECCIONALES',
                        'tipo' => 'pares',
                        'filas' => [
                            [
                                ['etiqueta' => 'I. DEL.:', 'valor' => $electrico?->direccionales_izquierda_delantera],
                                ['etiqueta' => 'D. DEL.:', 'valor' => $electrico?->direccionales_derecha_delantera],
                            ],
                            [
                                ['etiqueta' => 'I. TRA.:', 'valor' => $electrico?->direccionales_izquierda_trasera],
                                ['etiqueta' => 'D. TRA.:', 'valor' => $electrico?->direccionales_derecha_trasera],
                            ],
                        ],
                    ],
                    [
                        'titulo' => 'AFINACIÓN DE MOTOR',
                        'tipo' => 'normal',
                        'filas' => [
                            ['etiqueta' => 'Tapa distribuidor/Bujías/Cables:', 'valor' => $afinacion?->tapa_distribuidor_bujias_cables],
                            ['etiqueta' => 'Fuel injection:', 'valor' => $afinacion?->fuel_injection],
                        ],
                    ],
                ],
            ],
            [
                'ancho' => '33',
                'secciones' => [
                    [
                        'titulo' => 'FRENOS',
                        'tipo' => 'pares',
                        'encabezados' => ['PASTILLAS', 'ROTORES'],
                        'filas' => [
                            [
                                ['etiqueta' => 'I. DEL.:', 'valor' => $frenos?->pastillas_izquierda_delantera],
                                ['etiqueta' => 'I. DEL.:', 'valor' => $frenos?->rotores_izquierda_delantera],
                            ],
                            [
                                ['etiqueta' => 'D. DEL.:', 'valor' => $frenos?->pastillas_derecha_delantera],
                                ['etiqueta' => 'D. DEL.:', 'valor' => $frenos?->rotores_derecha_delantera],
                            ],
                            [
                                ['etiqueta' => 'I. TRA.:', 'valor' => $frenos?->pastillas_izquierda_trasera],
                                ['etiqueta' => 'I. TRA.:', 'valor' => $frenos?->rotores_izquierda_trasera],
                            ],
                            [
                                ['etiqueta' => 'D. TRA.:', 'valor' => $frenos?->pastillas_derecha_trasera],
                                ['etiqueta' => 'D. TRA.:', 'valor' => $frenos?->rotores_derecha_trasera],
                            ],
                        ],
                    ],
                    [
                        'titulo' => 'PINZAS/CILINDROS DE RUEDA',
                        'tipo' => 'pares',
                        'filas' => [
                            [
                                ['etiqueta' => 'I. DEL.:', 'valor' => $frenos?->pinzas_cilindros_rueda_izquierda_delantera],
                                ['etiqueta' => 'D. DEL.:', 'valor' => $frenos?->pinzas_cilindros_rueda_derecha_delantera],
                            ],
                            [
                                ['etiqueta' => 'I. TRA.:', 'valor' => $frenos?->pinzas_cilindros_rueda_izquierda_trasera],
                                ['etiqueta' => 'D. TRA.:', 'valor' => $frenos?->pinzas_cilindros_rueda_derecha_trasera],
                            ],
                        ],
                    ],
                    [
                        'titulo' => 'ESCAPE',
                        'tipo' => 'normal',
                        'filas' => [
                            ['etiqueta' => 'Mofle/Convertidor catalítico:', 'valor' => $escape?->mofle_convertidor_catlitico],
                            ['etiqueta' => 'Sensores/Soportes/Tubos:', 'valor' => $escape?->sensores_soporte_tubos],
                        ],
                        'notas' => $escape?->notas,
                    ],
                ],
            ],
        ];
    @endphp

    <div class="flex flex-col gap-2 w-full h-full">
        <div class="flex flex-col h-25 gap-2 inspeccion-encabezado">
            <div class="flex flex-row h-4rem">
                <h1 class="titulopdf w-55">REPORTE DE INSPECCIÓN TÉCNICA DE VEHÍCULO MULTIPUNTO</h1>
                <div class="flex flex-row w-45">
                    <div class="w-40 flex items-center justify-center">
                        <img src="{{ asset('storage/logos/facturas/'.$empresa_emision['logo']) }}" alt="" class="img_contenida">
                    </div>
                    <h4 class="w-60 direccion-empresa">{!! $empresa_emision['direccion'] !!}</h4>
                </div>
            </div>

            <div class="flex flex-col contedor_bordes h-25">
                <div class="h-50 renglon">
                    {!! celdaInspeccionVehicular('Nombre', $empresa['nombre'], 40) !!}
                    {!! celdaInspeccionVehicular('No.', $datos['orden'], 15) !!}
                    {!! celdaInspeccionVehicular('Fecha', $entrada['fecha'], 15) !!}
                    {!! celdaInspeccionVehicular('Tel.', $datos['telefono'], 15) !!}
                    {!! celdaInspeccionVehicular('Km', $entrada['kilometraje'], 15) !!}
                </div>
                <div class="h-50 renglon2">
                    {!! celdaInspeccionVehicular('Vehículo', $vehiculo['marca'].' '.$vehiculo['modelo'].' '.$vehiculo['anio'], 50) !!}
                    {!! celdaInspeccionVehicular('VIN', $vehiculo['vin'], 18) !!}
                    {!! celdaInspeccionVehicular('Económico', $vehiculo['economico'], 17) !!}
                    {!! celdaInspeccionVehicular('Placas', $vehiculo['placas'], 15) !!}
                </div>
            </div>

            <div class="flex-1 flex gap-2 inspeccion-resumen">
                <div class="contedor_bordes w-50 flex items-center justify-center">
                    @if ($carro)
                        <img src="{{ asset($carro) }}" alt="Vehículo inspeccionado" class="img_contenida">
                    @endif
                </div>
                <div class="contedor_bordes w-50 gap-1 p-1 contenedor-mensaje">
                    <h3 class="texto-descripcion"><span class="encabezado-descripcion">Indicaciones:</span> {{ $datos['observaciones'] }}</h3>
                </div>
            </div>

            <div class="contedor_bordes inspection-legend flex flex-row justify-around items-center bg-1">
                <div class="flex w-fit gap-1 items-center"><i class="inspection-square"></i><span>Requiere atención inmediata</span></div>
                <div class="flex w-fit gap-1 items-center"><i class="inspection-triangle"></i><span>Puede requerir atención futura</span></div>
                <div class="flex w-fit gap-1 items-center"><i class="inspection-circle"></i><span>Inspeccionada y está bien ahora</span></div>
            </div>
        </div>

        <div class="h-65 inspection-panels">
            @foreach ([
                ['numero' => 26, 'titulo' => 'PUNTOS - INSPECCIÓN DE VEHÍCULO', 'clase' => 'inspection-panel--26', 'columnas' => $columnas26],
                ['numero' => 57, 'titulo' => 'PUNTOS - INSPECCIÓN DE VEHÍCULO', 'subtitulo' => '(Incluye todos los anteriores)', 'clase' => 'inspection-panel--57', 'columnas' => $columnas57],
            ] as $panel)
                <article class="inspection-panel {{ $panel['clase'] }}">
                    <header class="inspection-panel__title">
                        <span class="inspection-panel__number">{{ $panel['numero'] }}</span>
                        <strong>{{ $panel['titulo'] }}</strong>
                        @isset($panel['subtitulo'])
                            <small>{{ $panel['subtitulo'] }}</small>
                        @endisset
                    </header>

                    <div class="inspection-columns">
                        @foreach ($panel['columnas'] as $columna)
                            <div class="inspection-column inspection-column--{{ $columna['ancho'] }}">
                                @foreach ($columna['secciones'] as $seccion)
                                    <section class="inspection-section {{ !empty($seccion['compacta']) ? 'inspection-section--compact' : '' }}">
                                        @if ($seccion['titulo'] !== '')
                                            <h4 class="inspection-section__title {{ !empty($seccion['subtitulo']) ? 'inspection-section__title--secondary' : '' }}">
                                                {{ $seccion['titulo'] }}
                                            </h4>
                                        @endif

                                        @if ($seccion['tipo'] === 'liquidos')
                                            <div class="inspection-table-head inspection-table-head--liquids">
                                                <span>CONDICIÓN</span><span>OK</span><span>LLENO</span>
                                            </div>
                                            @foreach ($seccion['filas'] as $fila)
                                                <div class="inspection-row inspection-row--liquid">
                                                    {!! marcadoresInspeccionVehicular($fila['valor'], $estatusInspeccion) !!}
                                                    <span class="inspection-row__label">{{ $fila['etiqueta'] }}</span>
                                                    {!! casillaInspeccionVehicular($fila['ok']) !!}
                                                    {!! casillaInspeccionVehicular($fila['lleno']) !!}
                                                </div>
                                            @endforeach
                                        @elseif ($seccion['tipo'] === 'llantas')
                                            <div class="inspection-table-head inspection-table-head--tires">
                                                <span>PATRÓN DE DESGASTE/DAÑO</span><span>PRESIÓN</span>
                                            </div>
                                            @foreach ($seccion['filas'] as $fila)
                                                <div class="inspection-row inspection-row--tire">
                                                    {!! marcadoresInspeccionVehicular($fila['valor'], $estatusInspeccion) !!}
                                                    <span class="inspection-row__label">{{ $fila['etiqueta'] }}</span>
                                                    <span class="inspection-pressure">{{ $fila['presion'] }}</span>
                                                </div>
                                            @endforeach
                                        @elseif ($seccion['tipo'] === 'pares')
                                            @isset($seccion['encabezados'])
                                                <div class="inspection-pair-head">
                                                    @foreach ($seccion['encabezados'] as $encabezado)
                                                        <span>{{ $encabezado }}</span>
                                                    @endforeach
                                                </div>
                                            @endisset
                                            @foreach ($seccion['filas'] as $filaPar)
                                                <div class="inspection-pair-row">
                                                    @foreach ($filaPar as $fila)
                                                        <div class="inspection-pair-cell">
                                                            {!! marcadoresInspeccionVehicular($fila['valor'], $estatusInspeccion) !!}
                                                            <span class="inspection-row__label">{{ $fila['etiqueta'] }}</span>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endforeach
                                        @else
                                            @foreach ($seccion['filas'] as $fila)
                                                <div class="inspection-row">
                                                    {!! marcadoresInspeccionVehicular($fila['valor'], $estatusInspeccion) !!}
                                                    <span class="inspection-row__label">{{ $fila['etiqueta'] }}</span>
                                                    <span class="inspection-row__line"></span>
                                                </div>
                                            @endforeach
                                        @endif

                                        @if (array_key_exists('notas', $seccion))
                                            <p class="notas-inspeccion-vehicular">
                                                <strong class="encabezado-notas">NOTAS:</strong>
                                                {{ $seccion['notas'] ?? '' }}
                                            </p>
                                        @endif
                                    </section>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                </article>
            @endforeach
        </div>

        <div class="h-10 overflow-hidden flex flex-row justify-around inspeccion-firmas">
            <div class="w-40">
                <div class="flex h-70 items-end justify-center">
                    @if (!empty($firma_recibido))
                        <img src="{{ asset($firma_recibido) }}" alt="Firma de recibido" class="img_contenida">
                    @endif
                </div>
                <h3 class="descripcion_firma">Firma de recibido</h3>
            </div>
            <div class="w-40">
                <div class="flex h-70 items-end justify-center">
                    @if (!empty($firma_cliente))
                        <img src="{{ asset($firma_cliente) }}" alt="Firma del cliente" class="img_contenida">
                    @endif
                </div>
                <h3 class="descripcion_firma">Firma del cliente</h3>
            </div>
        </div>
    </div>
</body>
</html>

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title inertia>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @routes
        @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
        @inertiaHead
    </head>
    <body class="font-sans antialiased">
        @inertia
    </body>

<li>
    <i class=prueba></i>
    <ul>
        <div>
            @canany(['cfeB2023.index', 'cfeO2023.index', 'cfeeco.index'])
                <li>
                    <i class=prueba></i>
                    <ul>
                        <div>
                            @foreach (['BAJIO' => [
                                'permission'=>'cfeB2023.index',
                                'modules' => [
                                    'Gasolina' => 'GASOLINA BAJIO',
                                    'Diesel' => 'DIESEL BAJIO',
                                ],
                                ],'MORELIA' => [
                                    'permission'=>'cfeO2023.index',
                                    'modules' => [
                                        'Gasolina' => 'GASOLINA MORELIA',
                                        'Diesel' => 'DIESEL MORELIA',
                                    ],
                                ],'APATZINGAN' => [
                                    'permission'=>'cfeO2023.index',
                                    'modules' => [
                                        'Diesel' => 'DIESEL APATZINGAN',
                                    ],
                                ],'ZACAPU' => [
                                    'permission'=>'cfeO2023.index',
                                    'modules' => [
                                        'Gasolina' => 'GASOLINA ZACAPU',
                                    ],
                                ],'JIQUILPAN' => [
                                    'permission'=>'cfeO2023.index',
                                    'modules' => [
                                        'Gasolina' => 'GASOLINA JIQUILPAN',
                                        'Diesel' => 'DIESEL JIQUILPAN',
                                    ],
                                ],'DIVISIONALES' => [
                                    'permission'=>'cfeO2023.index',
                                    'modules' => [
                                        'Gasolina' => 'GASOLINA DIVISIONALES',
                                    ],
                                ]
                            ] as $zona => $subapp)
                                @can($subapp->permission)
                                    <li class="vista" data-id="2025CFEDIESEL">
                                        <i class=prueba></i>
                                        <ul>
                                            <div>
                                                @foreach ($subapp->modules as  $label => $contrato)
                                                    <li>
                                                        <i class=prueba></i>
                                                        <ul>
                                                            <div>
                                                                @foreach (['0'=>'Por Enviar','1'=>'Por Aprovar','4' =>'Por Facturar'] as $estatus => $label)
                                                                    <li>
                                                                        <a href="{{ route('2025.Presupuestos.View.estatus', ['contrato' =>$contrato, 'zona' => $zona, 'anio' => '2025', 'modulo' => 'CFE 1', 'estatus' => $estatus]) }}">
                                                                            <i class="fa fa-money"></i>{{ $label }}
                                                                        </a>
                                                                    </li>
                                                                @endforeach
                                                            </div>
                                                        </ul>
                                                        <a href="#">
                                                            <i class="fa fa-money"></i>{{ $label }}
                                                        </a>
                                                    </li> 
                                                @endforeach
                                            </div>
                                        </ul>
                                        <a href="#">
                                            <i class="fa fa-money"></i>{{ $zona }}
                                        </a>
                                    </li>
                                @endcan
                            @endforeach
                        </div>
                    </ul>
                    <a><i class="fas fa-users"></i><span>CFE</span></a>
                </li>
            @endcan
            @canany(['akumas.index', 'akumas2023.index', 'cfbForaneos.index'])
                <li>
                    <i class=prueba></i>
                    <ul>
                        <div>
                            @foreach ([
                                [
                                'permission' => 'akumas.index',
                                'zona' => 'GENERALES',
                                'label' => 'PUBLICO GENERAL',
                                ],[
                                'permission' => 'akumas2023.index',
                                'zona' => 'LOCALES',
                                'label' => 'CFB',
                                ],[
                                'permission' => 'akumas2023.index',
                                'zona' => 'FORANEOS',
                                'label' => 'FORANEOS',
                                ]
                            ] as $MODULO)
                                @can($MODULO->permission)
                                    <li>
                                        <i class=prueba></i>
                                        <ul>
                                            <div>
                                                @foreach (['0'=>'Por Enviar','1'=>'Por Aprovar','4' =>'Por Facturar'] as $estatus => $label)
                                                    <li>
                                                        <a href="{{ route('2025.Presupuestos.View.estatus', ['contrato' =>'GENERAL', 'zona' => $MODULO->zona, 'anio' => '2025', 'modulo' => 'CFB', 'estatus' => $estatus]) }}">
                                                            <i class="fa fa-money"></i>{{ $label }}
                                                        </a>
                                                    </li>
                                                @endforeach
                                            </div>
                                        </ul>
                                        <a href="#">
                                            <i class="fa fa-money"></i>{{ $MODULO->label }}
                                        </a>
                                    </li> 
                                @endcan
                            @endforeach
                        </div>
                    </ul>
                    <a><i class="fas fa-users"></i><span>CFB</span></a>
                </li>
            @endcan
            @canany(['akumas2023.index', 'cfbECOForaneos.index'])
                <li>
                    <i class=prueba></i>
                    <ul>
                        <div>
                             @foreach ([
                                [
                                'permission' => 'cfeB2023.index',
                                'zona' => 'ALTOZANO',
                                'label' => 'ALTOZANO',
                                ],[
                                'permission' => 'cfeB2023.index',
                                'zona' => 'OTROS',
                                'label' => 'EDENRED',
                                ],[
                                'permission' => 'cfeB2023.index',
                                'zona' => 'LOCALES',
                                'label' => 'ECO',
                                ],[
                                'permission' => 'cfeB2023.index',
                                'zona' => 'FORANEOS',
                                'label' => 'FORANEOS',
                                ]
                            ] as $MODULO)
                                @can($MODULO->permission)
                                    <li>
                                        <i class=prueba></i>
                                        <ul>
                                            <div>
                                                @foreach (['0'=>'Por Enviar','1'=>'Por Aprovar','4' =>'Por Facturar'] as $estatus => $label)
                                                    <li>
                                                        <a href="{{ route('2025.Presupuestos.View.estatus', ['contrato' =>'GENERAL', 'zona' => $MODULO->zona, 'anio' => '2025', 'modulo' => 'CFB', 'estatus' => $estatus]) }}">
                                                            <i class="fa fa-money"></i>{{ $label }}
                                                        </a>
                                                    </li>
                                                @endforeach
                                            </div>
                                        </ul>
                                        <a href="#">
                                            <i class="fa fa-money"></i>{{ $MODULO->label }}
                                        </a>
                                    </li> 
                                @endcan
                            @endforeach
                        </div>
                    </ul>
                    <a><i class="fas fa-users"></i><span>ECO</span></a>
                </li>
            @endcan
    </ul>
    <a><i class="fas fa-users"></i><span> Folios Pendientes</span></a>
</li>
                 <li>
                    <i class=prueba></i>
                    <ul>
                        <div>
                            @canany(['cfeB2023.index', 'cfeO2023.index', 'cfeeco.index'])
                                <li>
                                    <i class=prueba></i>
                                    <ul>
                                        <div>
                                            @foreach (['BAJIO' => [
                                                'permission'=>'cfeB2023.index',
                                                'modules' => [
                                                    'Gasolina' => 'GASOLINA BAJIO',
                                                    'Diesel' => 'DIESEL BAJIO',
                                                ],
                                                ],'MORELIA' => [
                                                    'permission'=>'cfeO2023.index',
                                                    'modules' => [
                                                        'Gasolina' => 'GASOLINA MORELIA',
                                                        'Diesel' => 'DIESEL MORELIA',
                                                    ],
                                                ],'APATZINGAN' => [
                                                    'permission'=>'cfeO2023.index',
                                                    'modules' => [
                                                        'Diesel' => 'DIESEL APATZINGAN',
                                                    ],
                                                ],'ZACAPU' => [
                                                    'permission'=>'cfeO2023.index',
                                                    'modules' => [
                                                        'Gasolina' => 'GASOLINA ZACAPU',
                                                    ],
                                                ],'JIQUILPAN' => [
                                                    'permission'=>'cfeO2023.index',
                                                    'modules' => [
                                                        'Gasolina' => 'GASOLINA JIQUILPAN',
                                                        'Diesel' => 'DIESEL JIQUILPAN',
                                                    ],
                                                ],'DIVISIONALES' => [
                                                    'permission'=>'cfeO2023.index',
                                                    'modules' => [
                                                        'Gasolina' => 'GASOLINA DIVISIONALES',
                                                    ],
                                                ]
                                            ] as $zona => $subapp)
                                                @can($subapp['permission'])
                                                    <li class="vista" data-id="2025CFEDIESEL">
                                                        <i class=prueba></i>
                                                        <ul>
                                                            <div>
                                                                @foreach ($subapp['modules'] as  $label => $contrato)
                                                                    <li>
                                                                        <i class=prueba></i>
                                                                        <ul>
                                                                            <div>
                                                                                @foreach (['0'=>'Por Enviar','1'=>'Por Aprovar','4' =>'Por Facturar'] as $estatus => $label2)
                                                                                    <li>
                                                                                        <a href="{{ route('2025.Presupuestos.View.estatus', ['contrato' =>$contrato, 'zona' => $zona, 'anio' => '2025', 'modulo' => 'CFE 1', 'estatus' => $estatus]) }}">
                                                                                            <i class="fa fa-money"></i>{{ $label2 }}
                                                                                        </a>
                                                                                    </li>
                                                                                @endforeach
                                                                            </div>
                                                                        </ul>
                                                                        <a href="#">
                                                                            <i class="fa fa-money"></i>{{ $label }}
                                                                        </a>
                                                                    </li> 
                                                                @endforeach
                                                            </div>
                                                        </ul>
                                                        <a href="#">
                                                            <i class="fa fa-money"></i>{{ $zona }}
                                                        </a>
                                                    </li>
                                                @endcan
                                            @endforeach
                                        </div>
                                    </ul>
                                    <a><i class="fas fa-users"></i><span>CFE</span></a>
                                </li>
                            @endcan
                            @canany(['akumas.index', 'akumas2023.index', 'cfbForaneos.index'])
                                <li>
                                    <i class=prueba></i>
                                    <ul>
                                        <div>
                                            @foreach ([
                                                [
                                                'permission' => 'akumas.index',
                                                'zona' => 'GENERALES',
                                                'label' => 'PUBLICO GENERAL',
                                                ],[
                                                'permission' => 'akumas2023.index',
                                                'zona' => 'LOCALES',
                                                'label' => 'CFB',
                                                ],[
                                                'permission' => 'akumas2023.index',
                                                'zona' => 'FORANEOS',
                                                'label' => 'FORANEOS',
                                                ]
                                            ] as $MODULO)
                                                @can($MODULO['permission'])
                                                    <li>
                                                        <i class=prueba></i>
                                                        <ul>
                                                            <div>
                                                                @foreach (['0'=>'Por Enviar','1'=>'Por Aprovar','4' =>'Por Facturar'] as $estatus => $label)
                                                                    <li>
                                                                        <a href="{{ route('2025.Presupuestos.View.estatus', ['contrato' =>'GENERAL', 'zona' => $MODULO['zona'], 'anio' => '2025', 'modulo' => 'CFB', 'estatus' => $estatus]) }}">
                                                                            <i class="fa fa-money"></i>{{ $label }}
                                                                        </a>
                                                                    </li>
                                                                @endforeach
                                                            </div>
                                                        </ul>
                                                        <a href="#">
                                                            <i class="fa fa-money"></i>{{ $MODULO['label'] }}
                                                        </a>
                                                    </li> 
                                                @endcan
                                            @endforeach
                                        </div>
                                    </ul>
                                    <a><i class="fas fa-users"></i><span>CFB</span></a>
                                </li>
                            @endcan
                            @canany(['akumas2023.index', 'cfbECOForaneos.index'])
                                <li>
                                    <i class=prueba></i>
                                    <ul>
                                        <div>
                                            @foreach ([
                                                [
                                                'permission' => 'cfeB2023.index',
                                                'zona' => 'ALTOZANO',
                                                'label' => 'ALTOZANO',
                                                ],[
                                                'permission' => 'cfeB2023.index',
                                                'zona' => 'OTROS',
                                                'label' => 'EDENRED',
                                                ],[
                                                'permission' => 'cfeB2023.index',
                                                'zona' => 'LOCALES',
                                                'label' => 'ECO',
                                                ],[
                                                'permission' => 'cfeB2023.index',
                                                'zona' => 'FORANEOS',
                                                'label' => 'FORANEOS',
                                                ]
                                            ] as $MODULO)
                                                @can($MODULO['permission'])
                                                    <li>
                                                        <i class=prueba></i>
                                                        <ul>
                                                            <div>
                                                                @foreach (['0'=>'Por Enviar','1'=>'Por Aprovar','4' =>'Por Facturar'] as $estatus => $label)
                                                                    <li>
                                                                        <a href="{{ route('2025.Presupuestos.View.estatus', ['contrato' =>'GENERAL', 'zona' => $MODULO['zona'], 'anio' => '2025', 'modulo' => 'CFB', 'estatus' => $estatus]) }}">
                                                                            <i class="fa fa-money"></i>{{ $label }}
                                                                        </a>
                                                                    </li>
                                                                @endforeach
                                                            </div>
                                                        </ul>
                                                        <a href="#">
                                                            <i class="fa fa-money"></i>{{ $MODULO['label'] }}
                                                        </a>
                                                    </li> 
                                                @endcan
                                            @endforeach
                                        </div>
                                    </ul>
                                    <a><i class="fas fa-users"></i><span>ECO</span></a>
                                </li>
                            @endcan
                    </ul>
                    <a><i class="fas fa-users"></i><span> Folios Pendientes</span></a>
                </li>
</html>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="{{ asset('css/pdf.css') }}">
</head>
<body class="pdf-vale-body">
@php
    $logoAkumas = 'data:image/png;base64,' .
        base64_encode(file_get_contents(public_path('logo_akumas.png')));
@endphp
@foreach ($paginasConceptos as $indicePagina => $paginaConceptos)
    <section @class([
        'vale-almacen',
        'divisor-vales' => $loop->odd,
    ])>
        <div class="grid grid-cols-vale-header items-stretch gap-2mm">
            <div class="relative overflow-hidden border">
                <img class="vale-brand-image absolute object-contain" src="{{ $logoAkumas }}" alt="AKUMAS">
            </div>
            <div>
                <div class="border bg-vale-blue p-12mm text-center text-13pt font-bold tracking-35px text-white">ORDEN DE COMPRA / VALE DE ALMACÉN</div>
                <div class="mt-1mm grid grid-cols-vale-meta gap-15mm text-65pt">
                    <div class="min-h-4mm border-b px-1mm py-07mm"><strong>Fecha:</strong> {{ $vale->created_at?->format('Y-m-d H:i:s') }}</div>
                    <div class="min-h-4mm border-b px-1mm py-07mm"><strong>Para:</strong> {{ $vale->destino }}</div>
                    <div class="min-h-4mm border-b px-1mm py-07mm"><strong>Almacén</strong></div>
                </div>
                <div class="mt-1mm grid grid-cols-3 border">
                    <div class="vale-signature text-center text-65pt"><strong class="block">PROVEEDOR</strong>Pedido - Hora</div>
                    <div class="vale-signature text-center text-65pt"><strong class="block">ALMACÉN</strong>Pedido - Hora</div>
                    <div class="vale-signature text-center text-65pt"><strong class="block">TÉCNICO</strong>Pedido - Hora</div>
                </div>
                <div class="mt-1mm text-65pt"><strong>Técnico:</strong> {{ mb_strtoupper($vale->user?->name ?? '') }}</div>
            </div>
            <div class="border text-center">
                <div class="vale-folio-label text-8pt font-bold">FOLIO</div>
                <div class="vale-folio-number text-13pt font-bold text-red">No# {{ $folio }}</div>
                <div class="p-05mm text-57pt font-bold leading-13 text-vale-blue">Por favor realice el pedido<br>para lo siguiente</div>
            </div>
        </div>

        <table class="vale-table vale-items mt-1mm text-75pt">
            <colgroup>
                <col class="w-13">
                <col class="w-4">
                <col class="w-56">
                <col class="w-9">
                <col class="w-9">
                <col class="w-9">
            </colgroup>
            <thead>
                <tr>
                    <th>CLAVE</th>
                    <th>CANT.</th>
                    <th>DESCRIPCIÓN</th>
                    <th></th>
                    <th></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            @foreach ($paginaConceptos['filas'] as $detalle)
                <tr>
                    <td>{{ $detalle['clave'] }}</td>
                    <td class="text-right">{{ $detalle['cantidad'] !== null ? number_format((float) $detalle['cantidad'], 2) : '' }}</td>
                    <td class="text-center font-semibold">{{ $detalle['descripcion'] }}</td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            @endforeach
            @for ($renglon = $paginaConceptos['renglones_usados']; $renglon < 10; $renglon++)
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            @endfor
            </tbody>
        </table>

        <table class="vale-table vale-vehicle mt-12mm text-center text-65pt">
            <colgroup>
                <col class="w-10">
                <col class="w-10">
                <col class="w-10">
                <col class="w-36">
                <col class="w-8">
                <col class="w-13">
                <col class="w-13">
            </colgroup>
            <thead>
                <tr>
                    <th># Económico</th>
                    <th>Placas</th>
                    <th>Ord. Ser.</th>
                    <th>Marca-Modelo-Año-Motor-Serie</th>
                    <th>KM</th>
                    <th>Autorizado</th>
                    <th>Recibido</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $vehiculo?->economico }}</td>
                    <td>{{ $vehiculo?->placas }}</td>
                    <td>{{ $orden->orden_servicio }}</td>
                    <td class="text-57pt leading-105">{{ mb_strtoupper($descripcionVehiculo) }}</td>
                    <td>{{ $kilometraje }}</td>
                    <td>{{ mb_strtoupper($vale->user?->name ?? '') }}</td>
                    <td></td>
                </tr>
            </tbody>
        </table>
        @if ($totalPaginas > 1)
            <div class="vale-page-number absolute text-55pt font-bold">Página {{ $indicePagina + 1 }} de {{ $totalPaginas }}</div>
        @endif
    </section>
@endforeach
</body>
</html>

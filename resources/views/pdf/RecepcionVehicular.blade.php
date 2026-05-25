<!-- resources/views/pdf/demo.blade.php -->

<!DOCTYPE html>
<html lang="es">
<head ca>
    <link rel="stylesheet" href="{{ asset('css/pdf.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

</head>
<body class="a4">
    <div class="flex flex-col gap-2 w-full h-full">

        <div class="flex flex-col gap-2 h-30">
            <div class="flex flex-row">
                <div class="contenedor_title"><h1>REPORTE DE RECEPCION DE VEHICULO</h1></div>
                {{-- <div class="contenedor_orden"><h1>No. <span class="textfolio">{{$orden}}</span></h1></div> --}}
            </div>
            <div class="flex justify-between gap-2 flex-1">
                <div class="w-80 gap-1 flex-col flex h-full">
                    <div class="contedor_bordes h-90">
                        <div class="renglon h-25"> </div>
                        <div class="renglon h-10"> </div>
                        <div class="renglon h-21"> </div>
                        <div class="renglon h-17"> </div>
                        <div class="renglon h-17"> </div>
                        <div class="h-8"> </div>
                    </div>
                    <div class="contedor_bordes flex-1 gap-1 flex flex-row text-08 justify-around items-center">
                        <span>D = Dañada</span>
                        <span>O = Operacional</span>
                        <span>F = Falta objeto</span>
                        <span><i class="fa-solid fa-check"></i> = Sin Daño</span>
                        <span>R = Ocupa Reparacion</span>
                        <span>NA = No Aplica</span>
                    </div>
                </div>
                <div class="flex-1 h-full flex flex-col">
                    <div class="contedor_bordes flex-1"> </div>
                </div>
            </div>
        </div>
        <div class="h-15 flex gap-1">
            <div class="contedor_bordes w-full"></div>
            <div class="contedor_bordes w-full"></div>
        </div>
        <div class="h-15 flex gap-1">
            <div class="contedor_bordes w-full"></div>
            <div class="contedor_bordes w-full"></div>
        </div>
        <div class="h-17 contedor_bordes">
        </div>
        <div class="flex-1 flex contedor_bordes">

        </div>
    </div>

</body>
</html>
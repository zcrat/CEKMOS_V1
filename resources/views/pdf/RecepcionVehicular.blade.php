<!-- resources/views/pdf/demo.blade.php -->

<!DOCTYPE html>
<html lang="es">
<head ca>
    <link rel="stylesheet" href="{{ asset('css/pdf.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

</head>
<body class="a4 contedor_bordes">
    <div class="flex flex-col gap-2 w-full h-full">

        <div class="flex flex-col gap-2 h-30">
            <div class="flex flex-row">
                <div class="contenedor_title"><h1>REPORTE DE RECEPCION DE VEHICULO</h1></div>
            </div>
            <div class="flex justify-between gap-2 flex-1">
                <div class="w-80 gap-1 flex-col flex h-full">
                    <div class="contedor_bordes h-87">
                        <div class="renglon h-23"> 
                            <h3 class="celda w-45"><span>Nombre: </span>prechadssd s dd</h3>
                            <h3 class="celda w-35"><span>Cliente/Zona/Usuario: </span> </h3>
                            <h3 class="celda flex-1"><span>Ord. Servicio: </span>MOR70000</h3>
                            
                        </div>
                        <div class="renglon h-12"> 
                            <h3 class="celda"><span>Direccion: </span> </h3>
                        </div>
                        <div class="renglon h-20"> 
                            <h3 class="celda w-20"><span>Ciudad: </span> </h3>
                            <h3 class="celda w-20"><span>Estado: </span> </h3>
                            <h3 class="celda w-20"><span>CP: </span> </h3>
                            <h3 class="celda w-20"><span>Tel. Negocio: </span> </h3>
                            <h3 class="celda"><span>Tel. Casa: </span> </h3>
                        </div>
                        <div class="renglon h-16"> 
                            <h3 class="celda w-40"><span>Email: </span> </h3>
                            <h3 class="celda w-20"><span>Tel. Celular: </span> </h3>
                            <h3 class="celda w-20"><span>Gas Entrada: </span> </h3>
                            <h3 class="celda"><span>Gas Salida: </span> </h3>
                        </div>
                        <div class="renglon h-17"> 
                            <h3 class="celda w-12"><span>Año: </span> </h3>
                            <h3 class="celda w-14"><span>Marca: </span> </h3>
                            <h3 class="celda w-14"><span>Modelo: </span> </h3>
                            <h3 class="celda w-20"><span>Color: </span> </h3>
                            <h3 class="celda w-20"><span>Economico: </span> </h3>
                            <h3 class="celda"><span>Placas: </span> </h3>
                        </div>
                        <div class="renglon h-12  last"> 
                            <h3 class="celda w-26"><span>Km Entrada: </span></h3>
                            <h3 class="celda w-25"><span>Km Salida: </span> </h3>
                            <h3 class="celda"><span>Vin: </span></h3>
                        </div>
                    </div>
                    <div class="contedor_bordes flex-1 gap-1 flex flex-row text-08 justify-around items-center">
                        <span>D = Dañado</span>
                        <span>O = Operacional</span>
                        <span>F = Falta Objeto</span>
                        <span><i class="fa-solid fa-check"></i> = Sin Daño</span>
                        <span>R = Ocupa Reparacion</span>
                        <span>NA = No Aplica</span>
                    </div>
                </div>
                <div class="flex-1 h-full flex flex-col">
                    <div class="contedor_bordes flex-1"> 
                        <div class="renglon h-10"><h3 class="celda"><span>No: </span></h3></div>
                        <div class="renglon h-15"><h3 class="celda"><span>Ubicacion: </span></h3></div>
                        <div class="renglon h-15"><h3 class="celda"><span>Entrada: </span></h3></div>
                        <div class="renglon h-15"><h3 class="celda"><span>Recibido: </span></h3></div>
                        <div class="renglon h-15"><h3 class="celda"><span>Compromiso: </span></h3></div>
                        <div class="renglon h-15"><h3 class="celda"><span>Salida: </span></h3></div>
                        <div class="renglon last "><h3 class="celda"><span>Tecnico: </span></h3></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="h-15 flex gap-1">
            <div class="contedor_bordes w-full flex flex-col">
                <div class="contenedor_title"><h2>Condiciones De Interiores Y Equipo</h2></div>
                <div class="condiciones">
                    <div class="row">
                        <div class="col w-25"><h3>Puerta IF.</h3><span class="respuesta"> D </span></div>
                        <div class="col w-25"><h3>Puerta DF.</h3><span class="respuesta">D</span></div>
                        <div class="col w-25"><h3>Puerta IT.</h3><span class="respuesta">D</span></div>
                        <div class="col w-25"><h3>Puerta DT.</h3><span class="respuesta">D</span></div>
                    </div>
                    <div class="row">
                        <div class="col w-25"><h3>Asiento IF.</h3><span class="respuesta">NA</span></div>
                        <div class="col w-25"><h3>Asiento DF.</h3><span class="respuesta">D</span></div>
                        <div class="col w-25"><h3>Asiento IT.</h3><span class="respuesta">D</span></div>
                        <div class="col w-25"><h3>Asiento DT.</h3><span class="respuesta">D</span></div>
                    </div>
                    <div class="h-8"></div>
                    <div class="row">
                        <div class="col w-33"><h3>Consola</h3><span class="respuesta">D</span></div>
                        <div class="col w-33"><h3>Claxon</h3><span class="respuesta">D</span></div>
                        <div class="col w-33"><h3>Tablero</h3><span class="respuesta">D</span></div>
                    </div>
                    <div class="row">
                        <div class="col w-33"><h3>Quemacocos</h3><span class="respuesta">D</span></div>
                        <div class="col w-33"><h3>Toldo</h3><span class="respuesta">D</span></div>
                        <div class="col w-33"><h3>Elevadores Elec.</h3><span class="respuesta">D</span></div>
                    </div>
                    <div  class="row">
                        <div class="col w-33"><h3>Luces Int.</h3><span class="respuesta">D</span></div>
                        <div class="col w-33"><h3>Seguros Elec.</h3><span class="respuesta">D</span></div>
                        <div class="col w-33"><h3>Tapetes</h3><span class="respuesta">D</span></div>
                    </div>
                    <div  class="row">
                        <div class="col w-33"><h3>Radio</h3><span class="respuesta">D</span></div>
                        <div class="col w-33"><h3>Retrovisor</h3><span class="respuesta">D</span></div>
                        <div class="col w-33"><h3>Climatizador</h3><span class="respuesta">D</span></div>
                    </div>
                    

                </div>

            </div>
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
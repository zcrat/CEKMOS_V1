<!-- resources/views/pdf/demo.blade.php -->

<!DOCTYPE html>
<html lang="es">
<head ca>
    <link rel="stylesheet" href="{{ asset('css/pdf.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

</head>
<body class="a4 contedor_bordes flex overflow">
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
        <div class="h-15 flex gap-1 ">
            <div class="contedor_bordes w-60 flex flex-col">
                <div class="contenedor_title"><h2>Condiciones Interiores Y Equipo</h2></div>
                <div class="condiciones grid-4">
                        <div class="col"><h3>Puerta IF.</h3><span class="respuesta"> D </span></div>
                        <div class="col"><h3>Puerta DF.</h3><span class="respuesta">D</span></div>
                        <div class="col"><h3>Puerta IT.</h3><span class="respuesta">D</span></div>
                        <div class="col"><h3>Puerta DT.</h3><span class="respuesta">D</span></div>
                        <div class="col"><h3>Asiento IF.</h3><span class="respuesta">NA</span></div>
                        <div class="col"><h3>Asiento DF.</h3><span class="respuesta">D</span></div>
                        <div class="col"><h3>Asiento IT.</h3><span class="respuesta">D</span></div>
                        <div class="col"><h3>Asiento DT.</h3><span class="respuesta">D</span></div>
                        <div class="col"><h3>Consola</h3><span class="respuesta">D</span></div>
                        <div class="col"><h3>Claxon</h3><span class="respuesta">D</span></div>
                        <div class="col"><h3>Tablero</h3><span class="respuesta">D</span></div>
                        <div class="col"><h3>Quemacocos</h3><span class="respuesta">D</span></div>
                        <div class="col"><h3>Toldo</h3><span class="respuesta">D</span></div>
                        <div class="col"><h3>Tapetes</h3><span class="respuesta">D</span></div>
                        <div class="col"><h3>Radio</h3><span class="respuesta">D</span></div>
                        <div class="col"><h3>Retrovisor</h3><span class="respuesta">D</span></div>
                        <div class="col"><h3>Luces Interior</h3><span class="respuesta">D</span></div>
                        <div class="col"><h3>Seguros Electricos</h3><span class="respuesta">D</span></div>
                        <div class="col"><h3>Elevadores Electricos.</h3><span class="respuesta">D</span></div>
                        <div class="col"><h3>Climatizador A.C</h3><span class="respuesta">D</span></div>
                    

                </div>

            </div>
            <div class="contedor_bordes w-40">
                <div class="contenedor_title"><h2>Condiciones Exteriores Y Equipo</h2></div>
                <div class="condiciones grid-2">
                    
                        <div class="col"><h3>Antena/Radio</h3><span class="respuesta">D</span></div>
                        <div class="col"><h3>Estribos</h3><span class="respuesta">D</span></div>

                        <div class="col"><h3>Antena Telefono</h3><span class="respuesta">D</span></div>
                        <div class="col"><h3>Guardafangos</h3><span class="respuesta">D</span></div>

                        <div class="col"><h3>Antena/C.B.</h3><span class="respuesta">D</span></div>
                        <div class="col"><h3>Parabrisas</h3><span class="respuesta">D</span></div>

                        <div class="col"><h3>Sistema Alarma</h3><span class="respuesta">D</span></div>
                        <div class="col"><h3>Limpiaparabrisas</h3><span class="respuesta">D</span></div>

                        <div class="col"><h3>Luces Exteriores</h3><span class="respuesta">D</span></div>
                        <div class="col"><h3>Espejos Laterales</h3><span class="respuesta">D</span></div>

                </div>
            </div>
        </div>
        <div class="h-15 flex gap-1">
            <div class="contedor_bordes w-60 flex flex-col">
                <div class="contenedor_title"><h2>Varios Equipos - INVENTARIO</h2></div>
                <div class="condiciones grid-2 px-4">
                    <div class="w-full flex justify-around col-span-2">
                        <div class="check"><i class="fa-regular fa-square"></i><h3>Gato</h3></div>
                        <div class="check"><i class="fa-regular fa-square-check"></i><h3>Extinguidor</h3></div>
                        <div class="check"><i class="fa-regular fa-square-check"></i><h3>Placas</h3></div>
                    </div>
                    <div class="check"><i class="fa-regular fa-square"></i><h3>LLantas Refaccion</h3></div>
                    <div class="check"><i class="fa-regular fa-square"></i><h3>Cubreruedas</h3></div>
                    <div class="check"><i class="fa-regular fa-square"></i><h3>Candado de Ruedas</h3></div>
                    <div class="check"><i class="fa-regular fa-square"></i><h3>Llave para Tuercas de Rueda</h3></div>
                    <div class="check"><i class="fa-regular fa-square"></i><h3>Triángulo de Seguridad</h3></div>
                    <div class="check"><i class="fa-regular fa-square"></i><h3>Cables para Corriente</h3></div>
                    <div class="check"><i class="fa-regular fa-square"></i><h3>Estuche de Herramientas</h3></div>
                    <div class="check"><i class="fa-regular fa-square"></i><h3>Tarjeta de Circulación</h3></div>
                 </div>
            </div>
           <div class="contedor_bordes w-40 flex flex-col">
                <div class="contenedor_title"><h2>Condiciones Pintura</h2></div>
                 <div class="condiciones grid-2 grip-gap-1">
                    <div class="check"><i class="fa-regular fa-square"></i><h3>Decolorada</h3></div>
                    <div class="check"><i class="fa-regular fa-square"></i><h3 class="font-6">Exceso De Rociado</h3></div>
                    <div class="check"><i class="fa-regular fa-square"></i><h3>Color No Igualado</h3></div>
                    <div class="check"><i class="fa-regular fa-square"></i><h3>Logos En Buen Estado</h3></div>
                    <div class="check"><i class="fa-regular fa-square"></i><h3>Exceso Rayones</h3></div>
                    <div class="check"><i class="fa-regular fa-square"></i><h3>LLuvia Acida</h3></div>
                    <div class="check"><i class="fa-regular fa-square"></i><h3>Daños Por Granizo</h3></div>
                    <div class="check"><i class="fa-regular fa-square"></i><h3>Pequeñas Grietas</h3></div>
                    <div class="check"><i class="fa-regular fa-square"></i><h3>Carroceria Con Golpes </h3></div>
                    <div class="check"><i class="fa-regular fa-square"></i><h3>Emblemas Completos</h3></div>
                 </div>
            </div>
        </div>
        <div class="h-17 flex gap-2">
            <div class="contedor_bordes w-70">
                <img src="{{ asset('carro.jpeg')}}" alt="" class="img_contenida">
            </div>
            <div class="flex-1 flex flex-col justify-end">
                <div class="flex h-70 items-end">
                    <img src="{{ asset('firma.jpeg')}}" alt="" class="img_contenida">
                </div>
                <h3 class="descripcion_firma">Firma Supervisor</h3>
            </div>
        </div>
        <div class="flex-1 flex  flex-col  gap-2">
            <div class="descripciones_contenedor">
                <div class="contedor_bordes w-50 p-1">
                    <h3 class="texto-descripcion"><span class="encabezado-descripcion">Notas: </span>prechadssd s dd</h3>
                </div>
                <div class="contedor_bordes w-50 p-1">
                    <h3 class="texto-descripcion"><span class="encabezado-descripcion">Indicaciones:</span>REVISION DE FRENOS GENERALES ddddddssss csaf x  dsa vf fssdfsv fgsvsdv fsvs sd ddddd ddddddddddddddd REVISION DEL SENSOR DE OXIGENO REVISION DE FRENOS GENERALES REVISION DEL SENSOR DE OXIGENO REVISION DE FRENOS GENERALES REVISION DEL SENSOR DE OXIGENO</h3>
                </div>
            </div>
            <div class="flex flex-row flex-1 items-center">
                <div class="contedor_bordes w-60 h-100 flex flex-col">
                    <h5 class="descripcion"> Hemos registrado los daños en su vehículo que no están relacionados con las reparaciones
                        autorizadas.
                        El que usted y nuestro representante hayan revisado estas áreas conjuntamente,
                        ambos podemos tener la seguridad del mejor servicio posible. Hemos indicado cada área de daño o
                        defecto,
                        junto con otros artículos diversos, por favor no dude en ayudarnos mientras llenamos este formato.</h5>
                    <div class="flex justify-around flex-1">
                        <div class="w-40">
                            <div class="flex h-70">
                                <img src="{{ asset('firma.jpeg')}}" alt="" class="img_contenida">
                            </div>
                            <h3 class="descripcion_firma">Firma de Recibido</h3>
                        </div>
                         <div class="w-40">
                            <div class="flex h-70">
                            </div>
                            <h3 class="descripcion_firma">Firma del Cliente</h3>
                        </div>
                    </div>
                </div>
                <div class="flex flex-col w-40 items-end">
                    <div class="h-50 flex items-center">
                        <img src="{{ asset('logo_akumas.png')}}" alt="" class="img_contenida">
                    </div>
                    <h4 class="h-50  direccion-empresa">
                        ECO IMPULSA, S.A. DE .C.V. PUERTO DE ACAPULCO #328, RINCON DEL ANGEL. C.P. 58337, MORELIA, MICH, TEL (433) 2532182
                    </h4>
                </div>
            </div>

        </div>
    </div>

</body>
</html>
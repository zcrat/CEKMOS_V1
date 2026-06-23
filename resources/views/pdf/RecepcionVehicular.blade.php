<!-- resources/views/pdf/demo.blade.php -->

<!DOCTYPE html>
<html lang="es">
<head ca>
    <link rel="stylesheet" href="{{ asset('css/pdf.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

</head>
<body class="a4 contedor_bordes flex overflow">
    @php
        function condicionesEquipo($valor) {
            return match ($valor) {
                14 => '<i class="fa-solid fa-check"></i>',
                15 => "O",
                16 => "F",
                17 => "D",
                18 => "R",
                default => 'NA'
            };
        }
        function check($valor) {
            return match ($valor) {
                '1' => '<i class="fa-regular fa-square-check"></i>',
                default => '<i class="fa-regular fa-square"></i>'
            };
        }
    @endphp
    <div class="flex flex-col gap-2 w-full h-full">
        <div class="flex flex-col gap-2 h-30">
            <div class="flex flex-row">
                <div class="contenedor_title"><h1>REPORTE DE RECEPCION DE VEHICULO</h1></div>
            </div>
            <div class="flex justify-between gap-2 flex-1">
                <div class="w-80 gap-1 flex-col flex h-full">
                    <div class="contedor_bordes h-87">
                        <div class="renglon h-23"> 
                            <h3 class="celda w-45"><span>Nombre:</span>{{$empresa['nombre']}}</h3>
                            <h3 class="celda w-35"><span>Cliente/Zona/Usuario:</span>{{$datos['cliente']}}</h3>
                            <h3 class="celda flex-1"><span>Ord. Seguimiento: </span>{{$datos['seguimiento']}}</h3>
                        </div>
                        <div class="renglon h-12"> 
                            <h3 class="celda"><span>Direccion:</span>{{$empresa['calle']}}</h3>
                        </div>
                        <div class="renglon h-20"> 
                            <h3 class="celda w-20"><span>Ciudad:</span>{{$empresa['ciudad']}}</h3>
                            <h3 class="celda w-20"><span>Estado:</span>{{$empresa['estado']}}</h3>
                            <h3 class="celda w-20"><span>CP:</span>{{$empresa['cp']}}</h3>
                            <h3 class="celda w-20"><span>Tel. Negocio:</span>{{$empresa['negocio']}}</h3>
                            <h3 class="celda"><span>Tel. Casa: </span>{{$empresa['casa']}}</h3>
                        </div>
                        <div class="renglon h-16"> 
                            <h3 class="celda w-40"><span>Email:</span>{{$empresa['email']}}</h3>
                            <h3 class="celda w-20"><span>Tel. Celular:</span>{{$datos['telefono']}}</h3>
                            <h3 class="celda w-20"><span>Gas Entrada:</span>{{$entrada['gasolina']}}</h3>
                            <h3 class="celda"><span>Gas Salida:</span>{{$salida['gasolina']}}</h3>
                        </div>
                        <div class="renglon h-17"> 
                            <h3 class="celda w-12"><span>Año: </span>{{$vehiculo['anio']}}</h3>
                            <h3 class="celda w-14"><span>Marca: </span>{{$vehiculo['marca']}}</h3>
                            <h3 class="celda w-14"><span>Modelo: </span>{{$vehiculo['modelo']}}</h3>
                            <h3 class="celda w-20"><span>Color: </span>{{$vehiculo['color']}}</h3>
                            <h3 class="celda w-20"><span>Economico: </span>{{$vehiculo['economico']}}</h3>
                            <h3 class="celda"><span>Placas: </span>{{$vehiculo['placas']}}</h3>
                        </div>
                        <div class="renglon h-12  last"> 
                            <h3 class="celda w-26"><span>Km Entrada: </span>{{$entrada['kilometraje']}}</h3>
                            <h3 class="celda w-25"><span>Km Salida: </span>{{$salida['kilometraje']}}</h3>
                            <h3 class="celda"><span>Vin: </span>{{$vehiculo['vin']}}</h3>
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
                        <div class="renglon h-10"><h3 class="celda"><span>No:</span>{{$datos['orden']}}</h3></div>
                        <div class="renglon h-15"><h3 class="celda"><span>Ubicacion:{{$datos['ubicacion']}}</span></h3></div>
                        <div class="renglon h-15"><h3 class="celda"><span>Entrada:{{$entrada['fecha']}}</span></h3></div>
                        <div class="renglon h-15"><h3 class="celda"><span>Recibido:{{$datos['usuario']}}</span></h3></div>
                        <div class="renglon h-15"><h3 class="celda"><span>Compromiso:{{$entrada['estimacion']}}</span></h3></div>
                        <div class="renglon h-15"><h3 class="celda"><span>Salida: </span>{{$salida['fecha']}}</h3></div>
                        <div class="renglon last "><h3 class="celda"><span>Tecnico: </span>{{$datos['tecnico']}}</h3></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="h-15 flex gap-1 ">
            <div class="contedor_bordes w-60 flex flex-col">
                <div class="contenedor_title"><h2>Condiciones Interiores Y Equipo</h2></div>
                <div class="condiciones grid-4">
                        <div class="col"><h3>Puerta IF.</h3><span class="respuesta">{!!condicionesEquipo($interiores['pif'])!!}</span></div>
                        <div class="col"><h3>Puerta DF.</h3><span class="respuesta">{!!condicionesEquipo($interiores['pdf'])!!}</span></div>
                        <div class="col"><h3>Puerta IT.</h3><span class="respuesta">{!!condicionesEquipo($interiores['pit'])!!}</span></div>
                        <div class="col"><h3>Puerta DT.</h3><span class="respuesta">{!!condicionesEquipo($interiores['pdt'])!!}</span></div>
                        <div class="col"><h3>Asiento IF.</h3><span class="respuesta">{!!condicionesEquipo($interiores['aif'])!!}</span></div>
                        <div class="col"><h3>Asiento DF.</h3><span class="respuesta">{!!condicionesEquipo($interiores['adf'])!!}</span></div>
                        <div class="col"><h3>Asiento IT.</h3><span class="respuesta">{!!condicionesEquipo($interiores['ait'])!!}</span></div>
                        <div class="col"><h3>Asiento DT.</h3><span class="respuesta">{!!condicionesEquipo($interiores['adt'])!!}</span></div>
                        <div class="col"><h3>Consola</h3><span class="respuesta">{!!condicionesEquipo($interiores['consola'])!!}</span></div>
                        <div class="col"><h3>Claxon</h3><span class="respuesta">{!!condicionesEquipo($interiores['claxon'])!!}</span></div>
                        <div class="col"><h3>Tablero</h3><span class="respuesta">{!!condicionesEquipo($interiores['tablero'])!!}</span></div>
                        <div class="col"><h3>Quemacocos</h3><span class="respuesta">{!!condicionesEquipo($interiores['quemacocos'])!!}</span></div>
                        <div class="col"><h3>Toldo</h3><span class="respuesta">{!!condicionesEquipo($interiores['toldo'])!!}</span></div>
                        <div class="col"><h3>Tapetes</h3><span class="respuesta">{!!condicionesEquipo($interiores['tapetes'])!!}</span></div>
                        <div class="col"><h3>Radio</h3><span class="respuesta">{!!condicionesEquipo($interiores['radio'])!!}</span></div>
                        <div class="col"><h3>Retrovisor</h3><span class="respuesta">{!!condicionesEquipo($interiores['retrovisor'])!!}</span></div>
                        <div class="col"><h3>Luces Interior</h3><span class="respuesta">{!!condicionesEquipo($interiores['luces_interior'])!!}</span></div>
                        <div class="col"><h3>Seguros Electricos</h3><span class="respuesta">{!!condicionesEquipo($interiores['seguros_electricos'])!!}</span></div>
                        <div class="col"><h3>Elevadores Electricos.</h3><span class="respuesta">{!!condicionesEquipo($interiores['elevadores_electricos'])!!}</span></div>
                        <div class="col"><h3>Climatizador A.C</h3><span class="respuesta">{!!condicionesEquipo($interiores['climatizador'])!!}</span></div>
                </div>

            </div>
            <div class="contedor_bordes w-40">
                <div class="contenedor_title"><h2>Condiciones Exteriores Y Equipo</h2></div>
                <div class="condiciones grid-2">
                    
                        <div class="col"><h3>Antena/Radio</h3><span class="respuesta">{!!condicionesEquipo($exteriores['antena_radio'])!!}</span></div>
                        <div class="col"><h3>Estribos</h3><span class="respuesta">{!!condicionesEquipo($exteriores['estribos'])!!}</span></div>

                        <div class="col"><h3>Antena Telefono</h3><span class="respuesta">{!!condicionesEquipo($exteriores['antena_telefono'])!!}</span></div>
                        <div class="col"><h3>Guardafangos</h3><span class="respuesta">{!!condicionesEquipo($exteriores['guardafangos'])!!}</span></div>

                        <div class="col"><h3>Antena/C.B.</h3><span class="respuesta">{!!condicionesEquipo($exteriores['antena_cb'])!!}</span></div>
                        <div class="col"><h3>Parabrisas</h3><span class="respuesta">{!!condicionesEquipo($exteriores['parabrisas'])!!}</span></div>

                        <div class="col"><h3>Sistema Alarma</h3><span class="respuesta">{!!condicionesEquipo($exteriores['sistema_alarma'])!!}</span></div>
                        <div class="col"><h3>Limpiaparabrisas</h3><span class="respuesta">{!!condicionesEquipo($exteriores['limpiaparabrisas'])!!}</span></div>

                        <div class="col"><h3>Luces Exteriores</h3><span class="respuesta">{!!condicionesEquipo($exteriores['luces_exteriores'])!!}</span></div>
                        <div class="col"><h3>Espejos Laterales</h3><span class="respuesta">{!!condicionesEquipo($exteriores['espejos_laterales'])!!}</span></div>

                </div>
            </div>
        </div>
        <div class="h-15 flex gap-1">
            <div class="contedor_bordes w-60 flex flex-col">
                <div class="contenedor_title"><h2>Varios Equipos - INVENTARIO</h2></div>
                <div class="condiciones grid-2 px-4">
                    <div class="w-full flex justify-around col-span-2">
                        <div class="check">{!!check($inventario['gato'])!!}<h3>Gato</h3></div>
                        <div class="check">{!!check($inventario['extinguidor'])!!}<h3>Extinguidor</h3></div>
                        <div class="check">{!!check($inventario['placas'])!!}<h3>Placas</h3></div>
                    </div>
                    <div class="check">{!!check($inventario['llantas_refaccion'])!!}<h3>LLantas Refaccion</h3></div>
                    <div class="check">{!!check($inventario['cubreruedas'])!!}<h3>Cubreruedas</h3></div>
                    <div class="check">{!!check($inventario['candado_ruedas'])!!}<h3>Candado de Ruedas</h3></div>
                    <div class="check">{!!check($inventario['llave_tuercas'])!!}<h3>Llave para Tuercas de Rueda</h3></div>
                    <div class="check">{!!check($inventario['triangulo_seguridad'])!!}<h3>Triángulo de Seguridad</h3></div>
                    <div class="check">{!!check($inventario['cables_corriente'])!!}<h3>Cables para Corriente</h3></div>
                    <div class="check">{!!check($inventario['estuche_herramientas'])!!}<h3>Estuche de Herramientas</h3></div>
                    <div class="check">{!!check($inventario['tarjeta_circulacion'])!!}<h3>Tarjeta de Circulación</h3></div>
                 </div>
            </div>
           <div class="contedor_bordes w-40 flex flex-col">
                <div class="contenedor_title"><h2>Condiciones Pintura</h2></div>
                 <div class="condiciones grid-2 grip-gap-1">
                    <div class="check">{!!check($pintura['decolorada'])!!}<h3>Decolorada</h3></div>
                    <div class="check">{!!check($pintura['exceso_rociado'])!!}<h3 class="font-6">Exceso De Rociado</h3></div>
                    <div class="check">{!!check($pintura['color_no_igualado'])!!}<h3>Color No Igualado</h3></div>
                    <div class="check">{!!check($pintura['logos_buen_estado'])!!}<h3>Logos En Buen Estado</h3></div>
                    <div class="check">{!!check($pintura['exceso_rayones'])!!}<h3>Exceso Rayones</h3></div>
                    <div class="check">{!!check($pintura['lluvia_acida'])!!}<h3>LLuvia Acida</h3></div>
                    <div class="check">{!!check($pintura['danos_granizo'])!!}<h3>Daños Por Granizo</h3></div>
                    <div class="check">{!!check($pintura['pequenas_grietas'])!!}<h3>Pequeñas Grietas</h3></div>
                    <div class="check">{!!check($pintura['carroceria_golpes'])!!}<h3>Carroceria Con Golpes </h3></div>
                    <div class="check">{!!check($pintura['emblemas_completos'])!!}<h3>Emblemas Completos</h3></div>
                 </div>
            </div>
        </div>
        <div class="h-17 flex gap-2">
            <div class="contedor_bordes w-70">
                <img src="{{ asset($carro)}}" alt="" class="img_contenida">
            </div>
            <div class="flex-1 flex flex-col justify-end">
                <div class="flex h-70 items-end">
                    <img src="{{ asset($firma)}}" alt="" class="img_contenida">
                </div>
                <h3 class="descripcion_firma">Firma Supervisor</h3>
            </div>
        </div>
        <div class="flex-1 flex  flex-col  gap-2">
            <div class="descripciones_contenedor">
                <div class="contedor_bordes w-50 p-1">
                    <h3 class="texto-descripcion"><span class="encabezado-descripcion">Notas: </span>{{$datos['notas']}}</h3>
                </div>
                <div class="contedor_bordes w-50 p-1">
                    <h3 class="texto-descripcion"><span class="encabezado-descripcion">Indicaciones:</span>{{$datos['observaciones']}}</h3>
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
                                @if ($firma_recibido)
                                    <img src="{{ asset($firma_recibido)}}" alt="" class="img_contenida">
                                @endif
                            </div>
                            <h3 class="descripcion_firma">Firma de Recibido</h3>
                        </div>
                         <div class="w-40">
                            <div class="flex h-70">
                                @if ($firma_cliente)
                                    <img src="{{ asset($firma_cliente)}}" alt="" class="img_contenida">
                                @endif
                            </div>
                            <h3 class="descripcion_firma">Firma del Cliente</h3>
                        </div>
                    </div>
                </div>
                <div class="flex flex-col w-40 items-end">
                    <div class="h-50 flex items-center">
                        <img src="{{ asset($empresa_emision['logo'])}}" alt="" class="img_contenida">
                    </div>
                    <h4 class="h-50  direccion-empresa">
                        {!! $empresa_emision['direccion'] !!}
                    </h4>
                </div>
            </div>

        </div>
    </div>

</body>
</html>

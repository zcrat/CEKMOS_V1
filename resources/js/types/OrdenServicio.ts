  import {type option} from '@/types/generales'
  export interface EconomicoForm {
    id?:number,
    economico: string,
    placas: string,
    vin: string,
    anio:number|"",
    marca:string,
    modelo:string,
    tipo_id: string|null|undefined
  }
  export interface OrdenServicioForm {
    id?:number
    orden_seguimiento: string,
    orden_opcional: string,
    ubicacion: string,
    tipo_id: 5|6|7
    modulo_orden: number|string,
    vehiculo_concepto_id: option| null,
    empresa:option|null,
    cliente:option|null,
    telefono: number | null,
    estimacion: Date,
    kilometraje: number | null,
    gasolina: number | string,
    administrador: string,
    jefe: string,
    trabajador: string,
    tecnico: string,
    indicaciones_cliente: string,
    descripcion_mo: string,
    garantia: string,
    observaciones: string,
  }
  export interface ImagenesForm {
    ids?:{
      id:number
      DetallesGeneralesid:number
    },
    image:Blob,
    tipo_id:number
  }
  export interface PinturaForm{
    id?:number,
    DetallesGeneralesId?:number,
    decolarada:"1"|"0",
    color_desigual:"1"|"0",
    rayonnes:"1"|"0",
    grietas:"1"|"0",
    golpes:"1"|"0",
    emblemas:"1"|"0",
    logos:"1"|"0",
    rociado:"1"|"0",
    granizo:"1"|"0",
    lluvia:"1"|"0",
  }
  export interface InventarioForm{
    id?:number,
    DetallesGeneralesId?:number,
    llanta:"1"|"0",
    cables:"1"|"0",
    estuche:"1"|"0",
    llave_tuerca:"1"|"0",
    triangulo:"1"|"0",
    tarjeta_circulacion:"1"|"0",
    cubreruedas:"1"|"0",
    candado_rueda:"1"|"0",
    extinguidor:"1"|"0",
    gato:"1"|"0",
    placas:"1"|"0",
  }
  export interface CondicionesInterioresForm{
    id?:number,
    DetallesGeneralesId?:number,
    puerta_izq_f:string,
    puerta_izq_t:string,
    puerta_der_f:string,
    puerta_der_t:string,
    asiento_izq_f:string,
    asiento_izq_t:string,
    asiento_der_f:string,
    asiento_der_t:string,
    consola:string,
    claxon:string,
    tablero:string,
    quemacocos:string,
    toldo:string,
    elevadores:string,
    luces:string,
    seguros:string,
    climatizador:string,
    radio:string,
    retrovisor:string,
    tapetes:string,
  }
  export interface CondicionesExterioresForm{
    id?:number,
    DetallesGeneralesId?:number,
    antena_radio:string,
    estribos:string,
    antena_telefono:string,
    guardafangos:string,
    antena_cb:string,
    parabrisas:string,
    alarma:string,
    limpiaparabrisas:string,
    luces:string,
    espejos_laterales:string
  }
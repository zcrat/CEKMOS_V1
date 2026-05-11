import { color } from './../../types/generales';

import { CondicionesExterioresForm, CondicionesInterioresForm, DetallesGeneralesBaseProps, EconomicoForm, InventarioForm, PinturaForm } from "@/types/OrdenServicio"
import { sumarDiasSinDomingo } from '@/utils/functions/generales/fechas';
  
export const CondicionesExterioresInputs={
    'antena_radio':'ANTENA/RADIO',
    'estribos':'ESTRIBOS',
    'antena_telefono':'ANTENA/TELEFONO',
    'guardafangos':'GUARDAFANGOS',
    'antena_cb':'ANTENA/C.B',
    'parabrisas':'PARABRISAS',
    'alarma':'SISTEMA DE ALARMA',
    'limpiaparabrisas':'LIMPIAPARABRISAS',
    'luces':'LUCES EXTERIORES',
    'espejos_laterales':'ESPEJOS LATERALES'
  }
export const CondicionesInterioresInputs={
    'PANALES DE PUERTA':{
      'puerta_izq_f':'IZQUIERDA FRONTAL',
      'puerta_izq_t':'IZQUIERDA TRASERA',
      'puerta_der_f':'DERECHA FRONTAL',
      'puerta_der_t':'DERECHA TRASERA',
    },
    'ASIENTOS':{
      'asiento_izq_f':'IZQUIERDO FRONTAL',
      'asiento_izq_t':'IZQUIERDO TRASERO',
      'asiento_der_f':'DERECHO FRONTAL',
      'asiento_der_t':'DERECHO TRASERO',
    },
    'OTROS':{
      'consola':'CONSOLA CENTRAL',
      'claxon':'CLAXON',
      'tablero':'TABLERO',
      'quemacocos':'QUEMACOCOS',
      'toldo':'TOLDO',
      'elevadores':'ELEVADORES ELEC.',
      'luces':'LUCES INTERIORES',
      'seguros':'SEGUROS ELEC.',
      'climatizador':'CLIMATIZADOR',
      'radio':'RADIO',
      'retrovisor':'ESPEJO RETROVISOR',
      'tapetes':'TAPETES'
    }
  }
export const InventarioInputs={
    'llanta':'LLANTA REFACCION',
    'cables':'CABLES PARA CORRIENTE',
    'estuche':'ESTUCHE DE HERRAMIENTAS',
    'llave_tuerca':'LLAVE TUERCAS DE RUEDA',
    'triangulo':'TRIANGULO DE SEGURO',
    'tarjeta_circulacion':'TARJETA DE CIRCULACION',
    'cubreruedas':'CUBRERUEDAS',
    'candado_rueda':'CANDADO DE RUEDA',
    'extinguidor':'EXTINGUIDOR',
    'gato':'GATO',
    'placas':'PLACAS'
  }
export const PinturaInputs={
    'decolorada':'DECOLORADA',
    'color_desigual':'COLOR NO IGULADO',
    'rayones':'EXCESO DE RAYONES',
    'grietas':'PEQUEÑAS GRIETAS',
    'golpes':'CARROCERIA CON GOLPES',
    'emblemas':'EMBLEMAS COMPLETOS',
    'logos':'LOGOS EN BUEN ESTADO',
    'rociado':'EXCESO DE ROCIADO',
    'granizo':'DAÑOS POR GRANIZO',
    'lluvia':'LLUVIA ACIDA'
  }
export const DetallesGeneralesBase: DetallesGeneralesBaseProps ={
    id:undefined,
    orden_seguimiento: '',
    orden_opcional: '',
    ubicacion: '',
    tipo_id:7,
    modulo_orden:'',
    vehiculo:null,
    vehiculo_concepto_id: null,
    empresa: null,
    cliente:null,
    estimacion: sumarDiasSinDomingo(new Date(new Date().setHours(12,0)),2),
    kilometraje: null,
    gasolina: '',
    telefono: null,
    administrador: '',
    jefe: '',
    trabajador: '',
    tecnico: '',
    descripcion_mo: '',
    indicaciones_cliente: '',
    update_fotos: true,
}
export const InventarioBase: InventarioForm ={
    llanta:false,
    cables:false,
    estuche:false,
    llave_tuerca:false,
    triangulo:false,
    tarjeta_circulacion:false,
    cubreruedas:false,
    candado_rueda:false,
    extinguidor:false,
    gato:false,
    placas:false,
}
export const PinturaBase: PinturaForm ={
    decolorada:false,
    color_desigual:false,
    rayones:false,
    grietas:false,
    golpes:false,
    emblemas:false,
    logos:false,
    rociado:false,
    granizo:false,
    lluvia:false,
  }
export const CondicionesInterioresBase: CondicionesInterioresForm ={
    puerta_izq_f:"",
    puerta_izq_t:"",
    puerta_der_f:"",
    puerta_der_t:"",
    asiento_izq_f:"",
    asiento_izq_t:"",
    asiento_der_f:"",
    asiento_der_t:"",
    consola:"",
    claxon:"",
    tablero:"",
    quemacocos:"",
    toldo:"",
    elevadores:"",
    luces:"",
    seguros:"",
    climatizador:"",
    radio:"",
    retrovisor:"",
    tapetes:"",
}
export const CondicionesExterioresBase: CondicionesExterioresForm ={
    antena_radio:"",
    estribos:"",
    antena_telefono:"",
    guardafangos:"",
    antena_cb:"",
    parabrisas:"",
    alarma:"",
    limpiaparabrisas:"",
    luces:"",
    espejos_laterales:""
}
export const EconomicoBase: EconomicoForm ={
    id:undefined,
    economico: '',
    placas: '',
    vin: '',
    anio:'',
    marca:'',
    modelo:'',
    color:'',
    tipo_id: null
}
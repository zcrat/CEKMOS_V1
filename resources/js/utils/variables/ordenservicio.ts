import { CondicionesExterioresForm, CondicionesInterioresForm, EconomicoForm, InventarioForm, OrdenServicioForm, PinturaForm } from "@/types/OrdenServicio"
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
    'decolarada':'DECOLORADA',
    'color_desigual':'COLOR NO IGULADO',
    'rayonnes':'EXCESO DE RAYONES',
    'grietas':'PEQUEÑAS GRIETAS',
    'golpes':'CARROCERIA CON GOLPES',
    'emblemas':'EMBLEMAS COMPLETOS',
    'logos':'LOGOS EN BUEN ESTADO',
    'rociado':'EXCESO DE ROCIADO',
    'granizo':'DAÑOS POR GRANIZO',
    'lluvia':'LLUVIA ACIDA'
  }
export const DetallesGeneralesBase: OrdenServicioForm ={
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
    garantia:'LO ESTIPULADO EN EL CONTRATO',
    observaciones: 'DE ACUERDO A LO DIFICIL DE LA FALLA PARA SU REPARACION',//tiempo de entrega
}
export const InventarioBase: InventarioForm ={
    id:undefined,
    DetallesGeneralesId:undefined,
    llanta:"0",
    cables:"0",
    estuche:"0",
    llave_tuerca:"0",
    triangulo:"0",
    tarjeta_circulacion:"0",
    cubreruedas:"0",
    candado_rueda:"0",
    extinguidor:"0",
    gato:"0",
    placas:"0",
}
export const PinturaBase: PinturaForm ={
    id:undefined,
    DetallesGeneralesId:undefined,
    decolarada:"0",
    color_desigual:"0",
    rayonnes:"0",
    grietas:"0",
    golpes:"0",
    emblemas:"0",
    logos:"0",
    rociado:"0",
    granizo:"0",
    lluvia:"0",
  }
export const CondicionesInterioresBase: CondicionesInterioresForm ={
    id:undefined,
    DetallesGeneralesId:undefined,
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
    id:undefined,
    DetallesGeneralesId:undefined,
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
export const EconomicoDefault:EconomicoForm={ 
    id:undefined,
    economico: "",
    placas: "",
    vin: "",
    anio:"",
    marca:"",
    modelo:"",
    tipo_id:null
}
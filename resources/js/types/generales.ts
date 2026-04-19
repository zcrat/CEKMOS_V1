import { Ref } from "vue";
export interface modulosorden{
  id:number;
  descripcion:string;
}
export interface option{
  value:number|string;
  label:string;
}
export type zdbinario = "0" | "1";



export interface presupuestos{
  id:number;
  folio:string;
  orden:string;
  empresa:string;
  economico:string;
  placas:string;
  vin:string;
  creacion:string;
  estatus:string;
}

export interface paginationprops {
  api: string,
  params: Record<string, any>
  clases?: string;
  changesItems?:boolean;
}
export interface DatosPresupuestos {
  observaciones: string,
  descripcion_mo: string,
  garantia: string,
  folio: string,
  vigencia: Date|null,
  factura_id: number | null,
  tipo_id: number | null,
  estatus_id: number | null,
}

export interface DatosOrdenServicio {
  orden_servicio: string,
  orden_seguimiento: string,
  modulo_orden_id: number | null,
  vehiculo_id: number | null,
  vehiculo_concepto_id: number | null,
  user_id: number | null,
  empresa_id: number | null,
  cliente_id: number | null,
  update_fotos: boolean | null,
  diagnostico: Date | null,
  indicaciones_cliente: string,
  notas_mecanico: string,
  notas_retraso: string,
  telefono: string,
  ubicacion: string,
}
export interface DatosEntrada {
  orden_servicio_id?:number,
  fecha?: Date,
  estimacion: Date,
  kilometraje: number | null,
  gasolina: string
}
export interface Vehiculo {
  id?:number,
  placas:string,
  año:number|null,
  economico:string,
  vin:string,
  tipo_id:number|null,
  color_id:number|null,
  modelo_id:number|null
  modelo?:modelo,
  color?:color,
}
export interface marca {
  id?:number,
  descripcion:string,
}
export interface modelo {
  id?:number,
  descripcion:string,
  marca_id:number|null,
  marca?:marca,
}
export interface color {
  id?:number,
  descripcion:string
}
export interface ResponsablesNombres {
  administrador: string,
  jefe: string,
  trabajador: string,
  tecnico: string,
}
export interface NuevoPresupuesto {
  orden_servicio: string,
  folio: string,
  orden_seguimiento: string,
  ubicacion: string,
  telefono: number | null,
  empresa_id: number|null,
  cliente_id:number|null,
  gasolina: number | string,
  kilometraje: number | null,
  estimacion: Date,
  administrador: string,
  jefe: string,
  trabajador: string,
  tecnico: string,
  descripcion_mo: string,
  indicaciones_cliente: string,
  garantia: string,
  observaciones: string,//tiempo de entrega
  tipo_id: 5|6|7
  vehiculo_concepto_id: number| null,
  economico: string,
  placas: string,
  vin: string,
  marca: string,
  modelo: string,
  año: number|null,
  modulo_orden: number|string,
  //datos posibles
  vigencia: Date|null,
 }
 export interface NuevoOrdenServicio {
  orden_seguimiento: string,
  orden_opcional: string,
  ubicacion: string,
  tipo_presupuesto_id: number
  modulo_orden_id: number,
  vehiculo_concepto_id: number,
  empresa_id: number,
  telefono: number,
  cliente_id:number,
  vehiculo:{
    economico:string,
    placas:string,
    vin:string,
    year:string,
    marca:string,
    modelo:string,
  }
  gasolina: number | string,
  kilometraje: number | null,
  estimacion: Date,
  administrador: string,
  jefe: string,
  trabajador: string,
  tecnico: string,
  descripcion_mo: string,
  indicaciones_cliente: string,
  garantia: string,
  observaciones: string,//tiempo de entrega
  
  economico: string,
  placas: string,
  vin: string,
  marca: string,
  modelo: string,
  año: number|null,
  //datos posibles
  vigencia: Date|null,
 }
 export interface FormEmployee{
  name:string,
  paterno:string,
  materno:string|null,
  rfc:string,
  curp:string,
  regimen_fiscal:string,
  domicilio_fiscal:string
 }
 export interface FormUser{
  id?:number|null,
  name:string,
  paterno:string,
  materno:string|null,
  email:string,
  username:string,
  password:string,
  password_confirmation:string,
 }
 export interface VehiculoForm {
  id?:number,
  placas:string,
  economico:string,
  año:string,
  tipo_id:number|null,
  color:string,
  modelo:string,
  marca:string,
  vin:string,
  error?:{
    placas?:string[],
    economico?:string[],
    año?:string[],
    tipo_id?:string[],
    color?:string[],
    modelo?:string[],
    marca?:string[],
    vin?:string[],
  }
}
 export interface datagetpresupuestos{
    presupuesto: NuevoPresupuesto | null;
    empresa: option;
    cliente: option;
    vehiculo_concepto: option;
 }
  export interface PaginationProps {
    loading: Ref<boolean>,
    items: Ref<any[]>,
    params?:any
  }
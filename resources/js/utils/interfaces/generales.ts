export interface modulosorden{
  id:number;
  descripcion:string;
}
export interface option{
  value:number|string;
  label:string;
}

export interface presupuestos{
  id:number;
  folio:string;
  empresa:string;
  economico:string;
  placas:string;
  vin:string;
  creacion:string;
  estatus:string;
}

export interface paginationprops {
    onSearch: (currentPage: number, perPage: number) => void
    totalItems: number;
    totalPages: number;
    currentPage: number;
    itemsPerPage: number;
    clases?: string;
    chnagesItems?:boolean;
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
  tipo_id: 1|2|3//correctivo, preventivo,ambos
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
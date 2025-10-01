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
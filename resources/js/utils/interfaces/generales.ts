export interface modulosorden{
  id:number;
  descripcion:string;
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
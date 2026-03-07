export interface UsersTable{
    name:string;
    email:string;
    verified:boolean;
    date:Date;
    date_deleted?:Date|null;
    id:number;
}
export interface Employee{
    id:number;
    name:string;
    lastname1:string;
    lastname2:string;
    curp:string;
    rfc:string;
    regimen_fiscal:string;
    domicilio_fiscal:string;
}
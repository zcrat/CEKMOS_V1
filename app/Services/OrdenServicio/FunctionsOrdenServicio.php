<?php
namespace App\Services\OrdenServicio;

use App\Models\ModuloOrdenesServicio;
use App\Models\OrdenesServicio;
use App\Models\Presupuestos;

class FunctionsOrdenServicio
{
    public function GetClave(string $modulo_orden_id){
        $anteriores=0;
        $clave=ModuloOrdenesServicio::find($modulo_orden_id);
        $num=OrdenesServicio::withTrashed()->where('modulo_orden_id',$modulo_orden_id)->count() + 1;
        do{
            $numeroConCeros = str_pad($num + $anteriores, 5, "0", STR_PAD_LEFT);
            $orden= ($clave->clave??'Desc').$numeroConCeros;
            $anteriores++;
        }while(OrdenesServicio::withTrashed()->where('orden_servicio',$orden)->exists());
        return $orden;
    }
    public function GetFolio(string $ordenservicio_id,string $clave, int $tipo){

        $tipos=[5=>'C',6=>'P',7=>''];
        $num=Presupuestos::withTrashed()->where('orden_servicio_id',$ordenservicio_id)->count()  + 1;
        $numeroConCeros = str_pad($num, 2, "0", STR_PAD_LEFT);
        $folio= $clave.'-'.$numeroConCeros.$tipos[$tipo];
        return $folio;
    }
}
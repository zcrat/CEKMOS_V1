<?php
namespace App\Services;
use App\Models\User;
use App\Models\Vehiculos;
class Vehiculo
{
    public function CreateOrFind($placas, $economico, $vin, $marca_id, $modelo_id, $año){
        $vehiculo=Vehiculos::firstOrCreate(
            [
                'economico'=>$economico,
                'placas'=>$placas,
                'vin'=>$vin,
                'marca_id'=>$marca_id,
                'modelo_id'=>$modelo_id,
                'año'=>$año,
            ]
        );
        return response()->json($vehiculo);
    }
}
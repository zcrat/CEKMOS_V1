<?php
namespace App\Services;
use App\Models\User;
use App\Models\Vehiculos;
class Vehiculo
{
    public function CreateOrFind($placas, $economico, $vin, $marca_id, $modelo_id, $año){
        $vehiculo=Vehiculos::firstOrCreate(
            [
                'economico'=>$request->economico,
                'placas'=>$request->placas,
                'vin'=>$request->vin,
                'marca_id'=>$request->marca_id,
                'modelo_id'=>$request->modelo_id,
                'año'=>$request->año,
            ]
        );
        return response()->json($vehiculo);
    }
}
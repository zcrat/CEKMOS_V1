<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CajaMovimientos;
use Illuminate\Support\Facades\DB;

class MigrateDataBaseOld extends Controller
{
    public function migrateCaja(){
        $rows = DB::connection('mysql')
        ->table('otra_tabla')
        ->select(['concepto', 'ingreso', 'egreso', 'fecha'])
        ->get();

        foreach ($rows as $row) {
           CajaMovimientos::create([
                'descripcion' => $row->concepto,
                'ingreso'     => $row->ingreso,
                'egreso'      => $row->egreso,
                'fecha'       => $row->fecha,
            ]);
        }

    }
}

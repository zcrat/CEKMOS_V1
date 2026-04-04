<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Estados;
use Illuminate\Support\Facades\Http;
use App\Models\Municipios;
use Illuminate\Support\Facades\DB;

class EstadosandMunicipios extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
    $urlestado="https://gaia.inegi.org.mx/wscatgeo/v2/mgee/";
    $urlmunicipio="https://gaia.inegi.org.mx/wscatgeo/v2/mgem/";
    $allMunicipios = [];
    $response2 = Http::withoutVerifying()->get($urlestado);

    $estados = array_map(function($estado) {
        return [
            "id" => $estado["cve_ent"],
            "descripcion" => $estado["nomgeo"]
        ];
    }, $response2["datos"]);


    foreach($estados as $estado){
    $id=$estado['id'];
    Estados::create([
        'descripcion' => $estado['descripcion'],
        'clave' => $estado['id']
    ]);
    $response = Http::withoutVerifying()->get($urlmunicipio.$id);

    $municipios = array_map(function($municipio) {
        Municipios::create([
            'estado_id' => $municipio["cve_ent"],
            'clave' => $municipio["cve_mun"],
            'descripcion' => $municipio["nomgeo"],
        ]);
        return [
            "estado_id" => $municipio["cve_ent"],
            "clave" => $municipio["cve_mun"],
            "descripcion" => $municipio["nomgeo"]
        ];

    }, $response["datos"]);
    $allMunicipios = array_merge($allMunicipios, $municipios);
}
    }
}

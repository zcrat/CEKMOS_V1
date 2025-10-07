<?php

namespace Database\Seeders;

use App\Models\Modulos as ModulosModel;
use App\Models\Zonas;
use App\Models\Contratos;
use App\Models\Emisor;
use App\Models\ModuloOrdenesServicio;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class modulos extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Emisor::create([
            'n_certificado'=>'00001000000700780081',
            'archivo_cer'=>'CFB190523NF1.cer',
            'archivo_key'=>'CFB190523NF1.key',
            'clave_key'=>'cpJesusgro16',
            'rfc'=>'CFB190523NF1',
            'nombre'=>'CAR FIX AND BEYOND',
            'logotipo'=>'Logotipo_CFAB.png',
            'regimen'=>'601',
            'codigo'=>'58337',
            'serie_factura'=>'NS',
            'serie_p_factura'=>'CP',  
        ]);
        Emisor::create([
            'n_certificado'=>'00001000000511115283',
            'archivo_cer'=>'EIM110627PY3.cer',
            'archivo_key'=>'EIM110627PY3.key',
            'clave_key'=>'cpJesusgro16',
            'rfc'=>'EIM110627PY3',
            'nombre'=>'ECO IMPULSA',
            'logotipo'=>'Logotipo_Eco.png',
            'regimen'=>'601',
            'codigo'=>'58337',
            'serie_factura'=>'D',
            'serie_p_factura'=>'CP',  
        ]);
        Emisor::create([
            'n_certificado'=>'00001000000706999754',
            'archivo_cer'=>'GAPK7310075M6.cer',
            'archivo_key'=>'GAPK7310075M6.key',
            'clave_key'=>'cpJesusgro16',
            'rfc'=>'GAPK7310075M6',
            'nombre'=>'KARLA GARCIA PIZANOD',
            'logotipo'=>'Logotipo_CFAB.png',
            'regimen'=>'626',
            'codigo'=>'58000',
            'serie_factura'=>'KMG',
            'serie_p_factura'=>'CP',  
        ]);
        ModulosModel::create([
            'descripcion' => 'CFE',
            'emisor_id' => 1,
        ]);
        ModulosModel::create([
            'descripcion' => 'CFB',
            'emisor_id' => 1,
        ]);
        ModulosModel::create([
            'descripcion' => 'ECO',
            'emisor_id' => 2,
        ]);
        $zonas=['ZAMORA',
                'MORELIA',
                'JIQUILPAN',
                'ZACAPU',
                'BAJIO',
                'DIVISIONALES',
                'eliminar',
                'eliminar1',
                'GENERALES',
                'FORANEOS',
                'LOCALES',
                'ALTOZANO',
                'OTROS',
                'APATZINGAN'
        ];
        foreach ($zonas as $zona){
            Zonas::create([
                'descripcion' => $zona,
            ]);
        }
        $contratos = [
            ['descripcion' => 'ZONA ZAMORA 2024', 'numero' => '801094252', 'monto' => 3203844.01],
            ['descripcion' => 'GASOLINA MORELIA', 'numero' => '801142873', 'monto' => 2563324.99],
            ['descripcion' => 'GASOLINA JIQUILPAN', 'numero' => '801145714', 'monto' => 2224756.30],
            ['descripcion' => 'GASOLINA ZACAPU', 'numero' => '801145715', 'monto' => 448761.01],
            ['descripcion' => 'GASOLINA BAJIO', 'numero' => '9200014554', 'monto' => 1944295.62],
            ['descripcion' => 'ECO GENERALES', 'numero' => '9200013835', 'monto' => 753062.84],
            ['descripcion' => 'GASOLINA DIVISIONALES', 'numero' => '801142870', 'monto' => 263120.53],
            ['descripcion' => 'GENERAL', 'numero' => '000000000', 'monto' => 263120.53],
            ['descripcion' => 'DIESEL APATZINGAN', 'numero' => '801145924', 'monto' => 1423680.06],
            ['descripcion' => 'DIESEL JIQUILPAN', 'numero' => '801145925', 'monto' => 502825.49],
            ['descripcion' => 'DIESEL MORELIA', 'numero' => '801143473', 'monto' => 1800958.76],
            ['descripcion' => 'DIESEL BAJIO', 'numero' => '801153268', 'monto' => 822661.32],
        ];
        foreach ($contratos as $data) {
            Contratos::create($data);
        }
        
        $asignaciones = [
            ['modulo_id' => 1, 'zona_id' => 5,  'contrato_id' => 12, 'descripcion' => 'CFE BAJIO DIESEL',  'clave' => 'BAJ', 'año' => 2025],    
            ['modulo_id' => 1, 'zona_id' => 5,  'contrato_id' => 5,  'descripcion' => 'CFE BAJIO GASOLINA',  'clave' => 'BAJ',  'año' => 2025],
            ['modulo_id' => 1, 'zona_id' => 2,  'contrato_id' => 11, 'descripcion' => 'CFE MORELIA DIESEL',  'clave' => 'MOR',  'año' => 2025],
            ['modulo_id' => 1, 'zona_id' => 2,  'contrato_id' => 2,  'descripcion' => 'CFE MORELIA GASOLINA',  'clave' => 'MOR',   'año' => 2025],
            ['modulo_id' => 1, 'zona_id' => 6,  'contrato_id' => 7,  'descripcion' => 'CFE DIVISIONALES GASOLINA',  'clave' => 'DIV',  'año' => 2025],
            ['modulo_id' => 1, 'zona_id' => 4,  'contrato_id' => 4,  'descripcion' => 'CFE ZACAPU GASOLINA',  'clave' => 'ZAC',  'año' => 2025],
            ['modulo_id' => 1, 'zona_id' => 3,  'contrato_id' => 10, 'descripcion' => 'CFE JIQUILPAN DIESEL',  'clave' => 'JIQ', 'año' => 2025],
            ['modulo_id' => 1, 'zona_id' => 3,  'contrato_id' => 3,  'descripcion' => 'CFE JIQUILPAN GASOLINA',  'clave' => 'JIQ',  'año' => 2025],
            ['modulo_id' => 1, 'zona_id' => 9,  'contrato_id' => 9,  'descripcion' => 'CFE APATZINGAN DIESEL', 'clave' => 'APAT',  'año' => 2025],
            ['modulo_id' => 2, 'zona_id' => 13, 'contrato_id' => 8,  'descripcion' => 'CFB GENERALES', 'clave' => 'PG', 'año' => 2025],
            ['modulo_id' => 2, 'zona_id' => 11, 'contrato_id' => 8,  'descripcion' => 'CFB LOCALES', 'clave' => 'CFB', 'año' => 2025],
            ['modulo_id' => 2, 'zona_id' => 12, 'contrato_id' => 8,  'descripcion' => 'CFB FORANEOS', 'clave' => 'CFOR', 'año' => 2025],
            ['modulo_id' => 3, 'zona_id' => 10, 'contrato_id' => 8,  'descripcion' => 'ECO ALTOZANO', 'clave' => 'ALT', 'año' => 2025],
            ['modulo_id' => 3, 'zona_id' => 14, 'contrato_id' => 8,  'descripcion' => 'ECO EDENRED', 'clave' => 'EDEN', 'año' => 2025],
            ['modulo_id' => 3, 'zona_id' => 11, 'contrato_id' => 8,  'descripcion' => 'ECO LOCALES', 'clave' => 'ECO', 'año' => 2025],
            ['modulo_id' => 3, 'zona_id' => 12, 'contrato_id' => 8,  'descripcion' => 'ECO FORANEOS', 'clave' => 'EFOR', 'año' => 2025],
        ];
        foreach ($asignaciones as $data) {
            ModuloOrdenesServicio::create($data);
        }
    }
}

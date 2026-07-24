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
            'calle' => 'PUERTO DE ACAPULCO #328',
            'colonia' => 'RINCON DEL ANGEL',
            'ciudad' => 'MORELIA',
            'estado' => 'MICH',
            'cp' => '58337',
            'telefono' => '(433) 2532182',
            'logotipo'=>'Logotipo_CFAB.png',
            'regimen'=>'601',
            'codigo'=>'58337',
            'serie_factura'=>'NS',
            'serie_p_factura'=>'CP',  
        ]);
        Emisor::create([
            'n_certificado'=>'00001000000721806801',
            'archivo_cer'=>'EIM110627PY3.cer',
            'archivo_key'=>'EIM110627PY3.key',
            'clave_key'=>'KSO250913QE1',
            'rfc'=>'EIM110627PY3',
            'nombre'=>'ECO IMPULSA',
            'calle' => 'PUERTO DE ACAPULCO #328',
            'colonia' => 'RINCON DEL ANGEL',
            'ciudad' => 'MORELIA',
            'estado' => 'MICH',
            'cp' => '58337',
            'telefono' => '(433) 2532182',
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
            'calle' => 'PUERTO DE ACAPULCO #328',
            'colonia' => 'RINCON DEL ANGEL',
            'ciudad' => 'MORELIA',
            'estado' => 'MICH',
            'cp' => '58337',
            'telefono' => '(433) 2532182',
            'logotipo'=>'Logotipo_CFAB.png',
            'regimen'=>'626',
            'codigo'=>'58000',
            'serie_factura'=>'KMG',
            'serie_p_factura'=>'CP',  
        ]);
        Emisor::create([
            'n_certificado'=>'00001000000721639878',
            'archivo_cer'=>'KSO250913QE1.cer',
            'archivo_key'=>'KSO250913QE1.key',
            'clave_key'=>'KSO250913QE1',
            'rfc'=>'KSO250913QE1',
            'nombre'=>'KARWORKS SOLUTIONS',
            'calle' => 'VILLAS DEL MONTE #45',
            'colonia' => 'DESARROLLO MONARCA',
            'ciudad' => 'MORELIA',
            'estado' => 'MICH',
            'cp' => '58350',
            'telefono' => '(433) 4134234',
            'logotipo'=>'kws_factura.png',
            'regimen'=>'601',
            'codigo'=>'58350',
            'serie_factura'=>'KW',
            'serie_p_factura'=>'CP',  
        ]);
        ModulosModel::create([
            'descripcion' => 'CFE',
        ]);
        ModulosModel::create([
            'descripcion' => 'CFB',
        ]);
        ModulosModel::create([
            'descripcion' => 'ECO',
        ]);
        ModulosModel::create([
            'descripcion' => 'KARWORKS',
        ]);
        $zonas=['ZAMORA',
                'MORELIA',
                'JIQUILPAN',
                'ZACAPU',
                'BAJIO',
                'DIVISIONALES',
                'eliminar',
                'eliminar1',
                'APATZINGAN',
                'ALTOZANO',
                'LOCALES',
                'FORANEOS',
                'GENERALES',
                'OTROS',
        ];
        foreach ($zonas as $zona){
            Zonas::create([
                'descripcion' => $zona,
            ]);
        }
        $datosContratos = [
            'zamora_2024' => ['descripcion' => 'ZONA ZAMORA', 'tipo' => null, 'numero' => '801094252', 'monto' => 3203844.01, 'modulo_id' => 1, 'zona_id' => 1, 'año' => 2024],
            'morelia_gasolina' => ['descripcion' => 'MORELIA', 'tipo' => 'gasolina', 'numero' => '801142873', 'monto' => 2563324.99, 'modulo_id' => 1, 'zona_id' => 2, 'año' => 2025],
            'jiquilpan_gasolina' => ['descripcion' => 'JIQUILPAN', 'tipo' => 'gasolina', 'numero' => '801145714', 'monto' => 2224756.30, 'modulo_id' => 1, 'zona_id' => 3, 'año' => 2025],
            'zacapu_gasolina' => ['descripcion' => 'ZACAPU', 'tipo' => 'gasolina', 'numero' => '801145715', 'monto' => 448761.01, 'modulo_id' => 1, 'zona_id' => 4, 'año' => 2025],
            'bajio_gasolina' => ['descripcion' => 'BAJIO', 'tipo' => 'gasolina', 'numero' => '9200014554', 'monto' => 1944295.62, 'modulo_id' => 1, 'zona_id' => 5, 'año' => 2025],
            'eco_generales' => ['descripcion' => 'GENERALES', 'tipo' => null, 'numero' => '9200013835', 'monto' => 753062.84, 'modulo_id' => 3, 'zona_id' => 13, 'año' => 2025],
            'divisionales_gasolina' => ['descripcion' => 'DIVISIONALES', 'tipo' => 'gasolina', 'numero' => '801142870', 'monto' => 263120.53, 'modulo_id' => 1, 'zona_id' => 6, 'año' => 2025],
            'general' => ['descripcion' => 'GENERAL', 'tipo' => null, 'numero' => null, 'monto' => 263120.53, 'modulo_id' => 2, 'zona_id' => 13, 'año' => 2025],
            'apatzingan_diesel' => ['descripcion' => 'APATZINGAN', 'tipo' => 'diesel', 'numero' => '801145924', 'monto' => 1423680.06, 'modulo_id' => 1, 'zona_id' => 9, 'año' => 2025],
            'jiquilpan_diesel' => ['descripcion' => 'JIQUILPAN', 'tipo' => 'diesel', 'numero' => '801145925', 'monto' => 502825.49, 'modulo_id' => 1, 'zona_id' => 3, 'año' => 2025],
            'morelia_diesel' => ['descripcion' => 'MORELIA', 'tipo' => 'diesel', 'numero' => '801143473', 'monto' => 1800958.76, 'modulo_id' => 1, 'zona_id' => 2, 'año' => 2025],
            'bajio_diesel' => ['descripcion' => 'BAJIO', 'tipo' => 'diesel', 'numero' => '801153268', 'monto' => 822661.32, 'modulo_id' => 1, 'zona_id' => 5, 'año' => 2025],
            'morelia_karworks' => ['descripcion' => 'MORELIA', 'tipo' => 'gasolina', 'numero' => '801187670', 'monto' => 4579859.85, 'modulo_id' => 1, 'zona_id' => 2, 'año' => 2026],
            'divisionales_karworks' => ['descripcion' => 'DIVISIONALES', 'tipo' => 'gasolina', 'numero' => '801187669', 'monto' => 371200.00, 'modulo_id' => 1, 'zona_id' => 6, 'año' => 2026],
            'nadro' => ['descripcion' => 'NADRO', 'numero' => null, 'monto' => 822661.32, 'modulo_id' => 4, 'zona_id' => 10, 'año' => 2026],
            'dhl' => ['descripcion' => 'DHL', 'numero' => null, 'monto' => 822661.32, 'modulo_id' => 4, 'zona_id' => 10, 'año' => 2026],
            'farmacos' => ['descripcion' => 'FARMACOS', 'numero' => null, 'monto' => 822661.32, 'modulo_id' => 4, 'zona_id' => 10, 'año' => 2026],
            'triple_i_1800' => ['descripcion' => 'TRIPLE I 1800', 'numero' => null, 'monto' => 822661.32, 'modulo_id' => 4, 'zona_id' => 10, 'año' => 2026],
            'triple_i_servicios' => ['descripcion' => 'TRIPLE I SERVICIOS', 'numero' => null, 'monto' => 822661.32, 'modulo_id' => 4, 'zona_id' => 10, 'año' => 2026],
            'atlas_copco' => ['descripcion' => 'ATLAS COPCO', 'numero' => null, 'monto' => 822661.32, 'modulo_id' => 4, 'zona_id' => 10, 'año' => 2026],
            'ase_iii' => ['descripcion' => 'ASE III', 'numero' => null, 'monto' => 822661.32, 'modulo_id' => 4, 'zona_id' => 10, 'año' => 2026],
            'zacapu_2026' => ['descripcion' => 'ZACAPU', 'tipo' => null, 'numero' => '801191447', 'monto' => 1102000.00, 'modulo_id' => 1, 'zona_id' => 4, 'año' => 2026],
            'morelia_diesel_2026' => ['descripcion' => 'MORELIA', 'tipo' => 'diesel', 'numero' => '801197134', 'monto' => 1212121.00, 'modulo_id' => 1, 'zona_id' => 2, 'año' => 2026],
            'casanova_2026' => ['descripcion' => 'CASANOVA', 'tipo' => null, 'numero' => null, 'monto' => 0.00, 'modulo_id' => 3, 'zona_id' => 14, 'año' => 2026],
            'cf_correos_2026' => ['descripcion' => 'CORREOS', 'tipo' => null, 'numero' => null, 'monto' => 250000.00, 'modulo_id' => 4, 'zona_id' => 14, 'año' => 2026],
            'eco_correos_motos_2026' => ['descripcion' => 'CORREOS MOTOS', 'tipo' => null, 'numero' => null, 'monto' => 250000.00, 'modulo_id' => 4, 'zona_id' => 14, 'año' => 2026],
            'bajio_gasolina_2026' => ['descripcion' => 'BAJIO', 'tipo' => 'gasolina', 'numero' => '22222222222222', 'monto' => 2.00, 'modulo_id' => 1, 'zona_id' => 5, 'año' => 2026],
            'bajio_diesel_2026' => ['descripcion' => 'BAJIO', 'tipo' => 'diesel', 'numero' => '2222222222222', 'monto' => 2.00, 'modulo_id' => 1, 'zona_id' => 5, 'año' => 2026],
        ];
        $contratos = [];
        foreach ($datosContratos as $key => $data) {
            $contratos[$key] = Contratos::create($data);
        }
        
        $asignaciones = [
            ['modulo_id' => 1, 'zona_id' => 5,  'contrato_id' => $contratos['bajio_diesel']->id, 'descripcion' => '2025 CFE BAJIO DIESEL',  'clave' => 'BAJ', 'año' => 2025, 'emisor_id' => 1],
            ['modulo_id' => 1, 'zona_id' => 5,  'contrato_id' => $contratos['bajio_gasolina']->id,  'descripcion' => '2025 CFE BAJIO GASOLINA',  'clave' => 'BAJ',  'año' => 2025, 'emisor_id' => 1],
            ['modulo_id' => 1, 'zona_id' => 2,  'contrato_id' => $contratos['morelia_diesel']->id, 'descripcion' => '2025 CFE MORELIA DIESEL',  'clave' => 'MOR',  'año' => 2025, 'emisor_id' => 1],
            ['modulo_id' => 1, 'zona_id' => 2,  'contrato_id' => $contratos['morelia_gasolina']->id,  'descripcion' => '2025 CFE MORELIA GASOLINA',  'clave' => 'MOR',   'año' => 2025, 'emisor_id' => 1],
            ['modulo_id' => 1, 'zona_id' => 6,  'contrato_id' => $contratos['divisionales_gasolina']->id,  'descripcion' => '2025 CFE DIVISIONALES GASOLINA',  'clave' => 'DIV',  'año' => 2025, 'emisor_id' => 1],
            ['modulo_id' => 1, 'zona_id' => 4,  'contrato_id' => $contratos['zacapu_gasolina']->id,  'descripcion' => '2025 CFE ZACAPU GASOLINA',  'clave' => 'ZAC',  'año' => 2025, 'emisor_id' => 1],
            ['modulo_id' => 1, 'zona_id' => 3,  'contrato_id' => $contratos['jiquilpan_diesel']->id, 'descripcion' => '2025 CFE JIQUILPAN DIESEL',  'clave' => 'JIQ', 'año' => 2025, 'emisor_id' => 1],
            ['modulo_id' => 1, 'zona_id' => 3,  'contrato_id' => $contratos['jiquilpan_gasolina']->id,  'descripcion' => '2025 CFE JIQUILPAN GASOLINA',  'clave' => 'JIQ',  'año' => 2025, 'emisor_id' => 1],
            ['modulo_id' => 1, 'zona_id' => 9, 'contrato_id' => $contratos['apatzingan_diesel']->id,  'descripcion' => '2025 CFE APATZINGAN DIESEL', 'clave' => 'APAT',  'año' => 2025, 'emisor_id' => 1],
            ['modulo_id' => 2, 'zona_id' => 13, 'contrato_id' => null,  'descripcion' => '2025 CFB GENERALES', 'clave' => 'PG', 'año' => 2025, 'emisor_id' => 2],
            ['modulo_id' => 2, 'zona_id' => 11, 'contrato_id' => null,  'descripcion' => '2025 CFB LOCALES', 'clave' => 'CFB', 'año' => 2025, 'emisor_id' => 2],
            ['modulo_id' => 2, 'zona_id' => 12, 'contrato_id' => null,  'descripcion' => '2025 CFB FORANEOS', 'clave' => 'CFOR', 'año' => 2025, 'emisor_id' => 2],
            ['modulo_id' => 3, 'zona_id' => 10, 'contrato_id' => null,  'descripcion' => '2025 ECO ALTOZANO', 'clave' => 'ALT', 'año' => 2025, 'emisor_id' => 2],
            ['modulo_id' => 3, 'zona_id' => 14, 'contrato_id' => null,  'descripcion' => '2025 ECO EDENRED', 'clave' => 'EDEN', 'año' => 2025, 'emisor_id' => 2],
            ['modulo_id' => 3, 'zona_id' => 11, 'contrato_id' => null,  'descripcion' => '2025 ECO LOCALES', 'clave' => 'ECO', 'año' => 2025, 'emisor_id' => 2],
            ['modulo_id' => 3, 'zona_id' => 12, 'contrato_id' => null,  'descripcion' => '2025 ECO FORANEOS', 'clave' => 'EFOR', 'año' => 2025, 'emisor_id' => 2],
            ['modulo_id' => 4, 'zona_id' => 10, 'contrato_id' => null,  'descripcion' => '2026 KARWORKS ALTOZANO GENERALES', 'clave' => 'ALT', 'año' => 2026, 'emisor_id' => 4],
            ['modulo_id' => 1, 'zona_id' => 2, 'contrato_id' => $contratos['morelia_karworks']->id,  'descripcion' => '2026 CFE MORELIA GASOLINA', 'clave' => 'MOR', 'año' => 2026, 'emisor_id' => 4],
            ['modulo_id' => 1, 'zona_id' => 6, 'contrato_id' => $contratos['divisionales_karworks']->id,  'descripcion' => '2026 CFE DIVISIONALES GASOLINA', 'clave' => 'DIV', 'año' => 2026, 'emisor_id' => 4],
            ['modulo_id' => 4, 'zona_id' => 10, 'contrato_id' => $contratos['nadro']->id,  'descripcion' => '2026 KARWORKS INTEGRA NADRO', 'clave' => 'ALT', 'año' => 2026, 'emisor_id' => 4],
            ['modulo_id' => 4, 'zona_id' => 10, 'contrato_id' => $contratos['dhl']->id,  'descripcion' => '2026 KARWORKS INTEGRA DHL', 'clave' => 'ALT', 'año' => 2026, 'emisor_id' => 4],
            ['modulo_id' => 4, 'zona_id' => 10, 'contrato_id' => $contratos['farmacos']->id,  'descripcion' => '2026 KARWORKS INTEGRA FARMACOS', 'clave' => 'ALT', 'año' => 2026, 'emisor_id' => 4],
            ['modulo_id' => 4, 'zona_id' => 10, 'contrato_id' => $contratos['triple_i_1800']->id,  'descripcion' => '2026 KARWORKS INTEGRA TRIPLE I 1800', 'clave' => 'ALT', 'año' => 2026, 'emisor_id' => 4],
            ['modulo_id' => 4, 'zona_id' => 10, 'contrato_id' => $contratos['triple_i_servicios']->id,  'descripcion' => '2026 KARWORKS INTEGRA TRIPLE I SERVICIOS', 'clave' => 'ALT', 'año' => 2026, 'emisor_id' => 4],
            ['modulo_id' => 4, 'zona_id' => 10, 'contrato_id' => $contratos['atlas_copco']->id,  'descripcion' => '2026 KARWORKS INTEGRA ATLAS COPCO', 'clave' => 'ALT', 'año' => 2026, 'emisor_id' => 4],
            ['modulo_id' => 4, 'zona_id' => 10, 'contrato_id' => $contratos['ase_iii']->id,  'descripcion' => '2026 KARWORKS INTEGRA ASE III', 'clave' => 'ALT', 'año' => 2026, 'emisor_id' => 4],
            ['modulo_id' => 1, 'zona_id' => 4, 'contrato_id' => $contratos['zacapu_2026']->id, 'descripcion' => '2026 CFE ZACAPU', 'clave' => 'ZAC', 'año' => 2026, 'emisor_id' => 4],
            ['modulo_id' => 1, 'zona_id' => 2, 'contrato_id' => $contratos['morelia_diesel_2026']->id, 'descripcion' => '2026 CFE MORELIA DIESEL', 'clave' => 'MOR', 'año' => 2026, 'emisor_id' => 4],
            ['modulo_id' => 3, 'zona_id' => 14, 'contrato_id' => $contratos['casanova_2026']->id, 'descripcion' => '2026 ECO CASANOVA', 'clave' => 'CAS', 'año' => 2026, 'emisor_id' => 4],
            ['modulo_id' => 4, 'zona_id' => 14, 'contrato_id' => $contratos['cf_correos_2026']->id, 'descripcion' => '2026 KARWORKS CORREOS', 'clave' => 'COR', 'año' => 2026, 'emisor_id' => 4],
            ['modulo_id' => 4, 'zona_id' => 14, 'contrato_id' => $contratos['eco_correos_motos_2026']->id, 'descripcion' => '2026 KARWORKS CORREOS MOTOS', 'clave' => 'COR', 'año' => 2026, 'emisor_id' => 4],
            ['modulo_id' => 1, 'zona_id' => 5, 'contrato_id' => $contratos['bajio_gasolina_2026']->id, 'descripcion' => '2026 CFE BAJIO GASOLINA', 'clave' => 'BAJ', 'año' => 2026, 'emisor_id' => 4],
            ['modulo_id' => 1, 'zona_id' => 5, 'contrato_id' => $contratos['bajio_diesel_2026']->id, 'descripcion' => '2026 CFE BAJIO DIESEL', 'clave' => 'BAJ', 'año' => 2026, 'emisor_id' => 4],
        ];
        foreach ($asignaciones as $data) {
            ModuloOrdenesServicio::create($data);
        }
    }
}

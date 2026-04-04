<?php

namespace Database\Seeders;

use App\Models\Clientes;
use App\Models\Colores;
use App\Models\Empresas;
use App\Models\Marcas;
use App\Models\Modelos;
use App\Models\UsuariosTaller;
use App\Models\Vehiculos;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\VehiculosConceptos as ModelVehiculos;
use GuzzleHttp\Client;
use Symfony\Component\Console\Color;

class DatosPrueba extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    
    {
        $Empresa = Empresas::create([
            'nombre'=>'Empresa de Prueba',
            'rfc'=>'EPR123456789',
            'email'=>'empresa@prueba.com',
            'logo'=>'logo.png',
            'calle'=>'Calle de prueba',
            'cp'=>'12345',
            'municipio_id'=>1,
            'user_id'=>1,
            'regimen_fiscal_id'=>601,
            'telefono'=>'5555555555',
            'tel_celular'=>'5555555556',
            'tel_negocio'=>'5555555557',
        ]);
        Clientes::create([
            'empresa_id'=>$Empresa->id,
            'nombre'=>'Cliente de Prueba',
            'calle'=>'Calle de prueba',
            'cp'=>'12345',
            'municipio_id'=>1,
            'user_id'=>1,
            'email'=>'cliente@prueba.com',
            'telefono'=>'5555555558',
            'tel_celular'=>'5555555559',
            'tel_negocio'=>'555555560',
        ]);
        Colores::create([
            'descripcion'=>'Rojo',
        ]);
        Marcas::create([
            'descripcion'=>'Marca de Prueba',
        ]);
        Modelos::create([
            'marca_id'=>1,
            'descripcion'=>'Modelo de Prueba',
        ]);
        Vehiculos::create([
            'placas'=>'ABC123',
            'año'=>2020,
            'economico'=>'ECO123456',
            'vin'=>'VIN1234567890',
            'tipo_id'=>8,
            'color_id'=>1,
            'modelo_id'=>1,
        ]);
        UsuariosTaller::create([
            'tipo_id'=>1,
            'nombre'=>'Admnistrador',
        ]);
        UsuariosTaller::create([
            'tipo_id'=>2,
            'nombre'=>'Jefe de Taller',
        ]);
        UsuariosTaller::create([
            'tipo_id'=>3,
            'nombre'=>'Trabajador',
        ]);
        UsuariosTaller::create([
            'tipo_id'=>4,
            'nombre'=>'Tecnico',
        ]);
    }
}

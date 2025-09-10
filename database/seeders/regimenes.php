<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\RegimenesFiscalesModel;

class regimenes extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {  
        $regimenes = [
            ['id' => '601', 'nombre' => '601 - General de Ley Personas Morales'],
            ['id' => '603', 'nombre' => '603 - Personas Morales con Fines no Lucrativos'],
            ['id' => '605', 'nombre' => '605 - Sueldos y Salarios e Ingresos Asimilados a Salarios'],
            ['id' => '606', 'nombre' => '606 - Arrendamiento'],
            ['id' => '607', 'nombre' => '607 - Régimen de Enajenación o Adquisición de Bienes'],
            ['id' => '608', 'nombre' => '608 - Demas Ingresos'],
            ['id' => '609', 'nombre' => '609 - Consolidación'],
            ['id' => '610', 'nombre' => '610 - Residentes en el Extranjero sin Establecimiento Permanente en México'],
            ['id' => '611', 'nombre' => '611 - Ingresos por Dividendos (Socios y Accionistas)'],
            ['id' => '612', 'nombre' => '612 - Personas Físicas con Actividades Empresariales y Profesionales'],
            ['id' => '614', 'nombre' => '614 - Ingresos por Intereses'],
            ['id' => '615', 'nombre' => '615 - Régimen de los ingresos por obtención de premios'],
            ['id' => '616', 'nombre' => '616 - Sin Obligaciones Fiscales'],
            ['id' => '620', 'nombre' => '620 - Sociedades Cooperativas de Producción que optan por diferir sus ingresos'],
            ['id' => '621', 'nombre' => '621 - Incorporación Fiscal'],
            ['id' => '622', 'nombre' => '622 - Actividades Agrícolas, Ganaderas, Silvícolas y Pesqueras'],
            ['id' => '623', 'nombre' => '623 - Opcional para Grupos de Sociedades'],
            ['id' => '624', 'nombre' => '624 - Coordinados'],
            ['id' => '625', 'nombre' => '625 - Régimen de las Actividades Empresariales con ingresos a través de Plataformas Tecnológicas'],
            ['id' => '626', 'nombre' => '626 - Régimen Simplificado de Confianza'],
            ['id' => '628', 'nombre' => '628 - Hidrocarburos'],
            ['id' => '629', 'nombre' => '629 - De los Regímenes Fiscales Preferentes y de las Empresas Multinacionales'],
            ['id' => '630', 'nombre' => '630 - Enajenación de acciones en bolsa de valores']
        ];

        foreach ($regimenes as $r) {
            RegimenesFiscalesModel::create([
                'clave' => $r['id'],
                'descripcion' => substr($r['nombre'], strpos($r['nombre'], '-') + 2),
                'persona_fisica' => in_array($r['id'], ['605','606','607','608','611','612','614','615','616','621','625','626']),
                'persona_moral' => in_array($r['id'], ['601','603','609','610','620','622','623','624','628','629','630','626']),
            ]);
        }
    }
}

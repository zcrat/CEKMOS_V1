<?php

namespace App\Services\Validations;

class InventarioYCondicionesEquipo
{
    private const KEYS_INVENTARIO = [
        'llanta',
        'cables',
        'estuche',
        'llave_tuerca',
        'triangulo',
        'tarjeta_circulacion',
        'cubreruedas',
        'candado_rueda',
        'extinguidor',
        'gato',
        'placas',
    ];

    private const KEYS_PINTURA = [
        'decolorada',
        'color_desigual',
        'rayones',
        'grietas',
        'golpes',
        'emblemas',
        'logos',
        'rociado',
        'granizo',
        'lluvia',
    ];

    private const KEYS_CONDICIONES_INTERIOR = [
        'puerta_izq_f',
        'puerta_izq_t',
        'puerta_der_f',
        'puerta_der_t',
        'asiento_izq_f',
        'asiento_izq_t',
        'asiento_der_f',
        'asiento_der_t',
        'consola',
        'claxon',
        'tablero',
        'quemacocos',
        'toldo',
        'elevadores',
        'luces',
        'seguros',
        'climatizador',
        'radio',
        'retrovisor',
        'tapetes',
    ];

    private const KEYS_CONDICIONES_EXTERIOR = [
        'antena_radio',
        'estribos',
        'antena_telefono',
        'guardafangos',
        'antena_cb',
        'parabrisas',
        'alarma',
        'limpiaparabrisas',
        'luces',
        'espejos_laterales',
    ];

    public function rulesbasic(): array
    {
        $rules = [];

        foreach (self::KEYS_INVENTARIO as $key) {
            $rules["inventario.$key"] = ['required', 'in:0,1'];
        }

        foreach (self::KEYS_PINTURA as $key) {
            $rules["pintura.$key"] = ['required', 'in:0,1'];
        }

        foreach (self::KEYS_CONDICIONES_INTERIOR as $key) {
            $rules["condiciones_interiores.$key"] = ['required', 'exists:estatus,id'];
        }

        foreach (self::KEYS_CONDICIONES_EXTERIOR as $key) {
            $rules["condiciones_exteriores.$key"] = ['required', 'exists:estatus,id'];
        }

        return $rules;
    }
}

<?php

use App\Models\Marcas;
use App\Models\Modelos;
use App\Models\Motores;
use Illuminate\Database\QueryException;

test('un modelo puede tener variantes con motores diferentes', function () {
    $marca = Marcas::create(['descripcion' => 'ford']);
    $motorSeis = Motores::create(['descripcion' => '6 cilindros']);
    $motorOcho = Motores::create(['descripcion' => '8 cilindros']);

    $modeloSeis = Modelos::create([
        'descripcion' => 'f-150',
        'marca_id' => $marca->id,
        'motor_id' => $motorSeis->id,
    ]);
    $modeloOcho = Modelos::create([
        'descripcion' => 'f-150',
        'marca_id' => $marca->id,
        'motor_id' => $motorOcho->id,
    ]);

    expect($modeloSeis->id)->not->toBe($modeloOcho->id)
        ->and($modeloSeis->motor->descripcion)->toBe('6 cilindros')
        ->and($motorOcho->modelos()->first()->is($modeloOcho))->toBeTrue();
});

test('la combinacion de marca modelo y motor es unica', function () {
    $marca = Marcas::create(['descripcion' => 'ford']);
    $motor = Motores::create(['descripcion' => 'eléctrico']);
    $datos = [
        'descripcion' => 'mustang',
        'marca_id' => $marca->id,
        'motor_id' => $motor->id,
    ];

    Modelos::create($datos);

    expect(fn () => Modelos::create($datos))->toThrow(QueryException::class);
});

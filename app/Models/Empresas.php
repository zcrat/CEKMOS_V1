<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Empresas extends Model
{
    use SoftDeletes;
    protected $table = 'empresas';
    protected $fillable = [
        'nombre',
        'rfc',
        'email',
        'logo',
        'calle',
        'cp',
        'ciudad_id',
        'emisor_id',
        'user_id',
        'regimen_id',
        'telefono',
        'tel_celular',
        'tel_negocio',
    ];
    protected $casts = [];


}

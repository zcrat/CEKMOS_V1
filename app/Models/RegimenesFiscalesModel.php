<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class RegimenesFiscalesModel extends Model
{
    use SoftDeletes;
    protected $table = 'regimes_fiscales';
    protected $primaryKey = 'clave';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'clave',
        'descripcion',
        'persona_fisica',
        'persona_moral',
    ];
    protected $casts = [];

}
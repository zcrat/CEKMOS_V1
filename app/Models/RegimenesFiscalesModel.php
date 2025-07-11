<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class RegimenesFiscalesModel extends Model
{
    use SoftDeletes;
    protected $table = 'regimes_fiscales';
    protected $fillable = [
        'clave',
        'descripcion',
        'regimen_fiscal',
    ];
    protected $casts = [];

}

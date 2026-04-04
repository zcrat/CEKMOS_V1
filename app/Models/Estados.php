<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Estados extends Model
{
    use SoftDeletes;
    protected $table = 'estados';
    protected $fillable = [
        'descripcion',
        'clave'
    ];
    public function municipios(){
        return $this->hasMany(Municipios::class, 'estado_id');
    }
}

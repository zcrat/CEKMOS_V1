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
    ];
    public function ciudades()
    {
        return $this->hasMany(Ciudades::class, 'categoria_id');
    }
}

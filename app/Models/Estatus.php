<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Estatus extends Model
{
    use SoftDeletes;
    protected $table = 'estatus';
    protected $fillable = [
        'descripcion',
        'categoria_id'
    ];
    public function categoria()
    {
        return $this->belongsTo(Categorias::class, 'categoria_id');
    }
    public function Facturas()
    {
        return $this->hasMany(Facturas::class, 'estatus_id');
    }
    public function Presupuestos(){
        return $this->hasMany(Presupuestos::class,'estatus_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class ModulosPerUser extends Model
{
    use SoftDeletes;
    protected $table = 'modulos_per_users';
    protected $fillable = [
        'modulo_orden_id',
        'user_id'
    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function modulo_orden(){
        return $this->belongsTo(ModuloOrdenesServicio::class,'modulo_orden_id');
    }
}

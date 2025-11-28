<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Notificaciones extends Model
{
    use SoftDeletes;
    protected $table = 'notificaciones';
    protected $fillable = [
        'prioridad',
        'message',
        'title',
        'user_id',
        'tipo_id',
        'read_at',
    ];
    protected $casts = ['read_at'=>'datetime'];
    public function user(){
      return $this->belongsTo(User::class,'user_id');
    }
    public function tipo(){
      return $this->belongsTo(Tipos::class,'tipo_id');
    }
}

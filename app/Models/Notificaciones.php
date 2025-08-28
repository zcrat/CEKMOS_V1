<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class Notificaciones extends Model
{
    protected $table = 'notificaciones';
    protected $fillable = [
    'tipo',
    'prioridad',
    'message',
    'title',
    'user_id',
    'read_at',
    ];
}

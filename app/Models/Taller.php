<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Taller extends Model
{
    public $timestamps = false;

    protected $table = 'talleres';

    protected $fillable = [
        'descripcion',
        'externo',
    ];

    protected $casts = [
        'externo' => 'boolean',
    ];

    public function usuarios()
    {
        return $this->hasMany(User::class, 'taller_id');
    }

    public function ordenesServicio()
    {
        return $this->hasMany(OrdenesServicio::class, 'taller_id');
    }
}

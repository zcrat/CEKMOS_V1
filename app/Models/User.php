<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasRoles;
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];
protected $dateFormat = 'Y-m-d H:i:s.v'; // Formato compatible con SQL Server
 // formato compatible con SQL Server

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    public function modulos_orden(){
        return $this->hasMany(ModulosPerUser::class, 'user_id');
    }
    public function clientes(){
        return $this->hasMany(Clientes::class,'user_id'); 
    }
    public function empresas(){
        return $this->hasMany(Empresas::class,'user_id'); 
    }
    public function facturas(){
        return $this->hasMany(Facturas::class,'user_id'); 
    }
    public function notifications(){
        return $this->hasMany(Notificaciones::class,'user_id'); 
    }
    public function ordenes_servicio(){
        return $this->hasMany(OrdenesServicio::class,'user_id'); 
    }
    public function conceptos_presupuestos(){
        return $this->hasMany(ConceptosPerPresupuesto::class,'user_id');
    }
}

<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Spatie\Permission\Traits\HasRoles;


// cambios para que solo sea user y sustituyendo las relaciones logicas en lso modelos 
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;



class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    
    use HasFactory, Notifiable, TwoFactorAuthenticatable;
    use HasRoles;

    protected $primaryKey = 'id_usuario';

    public $incrementing = true;

    protected $keyType = 'int';


    
    protected $fillable = [
        'id_sucursal',
        'id_direccion',
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
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

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('');
    }

    // relaciones con los modelos

    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class, 'id_sucursal', 'id_sucursal');
    }

    public function direccion(): BelongsTo
    {
        return $this->belongsTo(Direccion::class, 'id_direccion', 'id_direccion');
    }

     public function turnos(): HasMany
    {
        return $this->hasMany(Turno::class, 'id_usuario', 'id_usuario');
    }

    public function manejadas(): HasMany
    {
        return $this->hasMany(Manejada::class, 'id_manejada', 'id_manejada');  
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Usuario extends Model
{
    use HasFactory;

    protected $table = 'usuario';
    protected $primaryKey = 'id_usuario';

    protected $fillable = [
        'id_sucursal',
        'id_direccion',
        'nombre',
        'correo',
        'password',
    ];

    protected $hidden = [
        'password',
    ];

    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class, 'id_sucursal', 'id_sucursal');
    }

    public function direccion(): BelongsTo
    {
        return $this->belongsTo(Direccion::class, 'id_direccion', 'id_direccion');
    }

    public function corridas(): HasMany
    {
        return $this->hasMany(Corrida::class, 'id_chofer', 'id_usuario');
    }

     public function turnos(): HasMany
    {
        return $this->hasMany(Turno::class, 'id_cajero', 'id_usuario');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Usuario extends Model
{
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

    public function gerente(): HasOne
    {
        return $this->hasOne(Gerente::class, 'id_usuario', 'id_usuario');
    }

    public function cajero(): HasOne
    {
        return $this->hasOne(Cajero::class, 'id_usuario', 'id_usuario');
    }

    public function chofer(): HasOne
    {
        return $this->hasOne(Chofer::class, 'id_usuario', 'id_usuario');
    }
}

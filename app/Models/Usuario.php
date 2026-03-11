<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
    use HasFactory;

    protected $table = 'usuarios';

    protected $fillable = [
        'nombre',
        'password',
        'correo',
        'direccion',
        'id_sucursal',
    ];

    protected $hidden = ['password'];

    public function sucursal(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Sucursal::class, 'id_sucursal');
    }

    public function gerente(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Gerente::class, 'id_usuario');
    }

    public function chofer(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Chofer::class, 'id_usuario');
    }

    public function cajero(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Cajero::class, 'id_usuario');
    }
}

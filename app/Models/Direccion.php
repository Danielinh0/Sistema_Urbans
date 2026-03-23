<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Direccion extends Model
{
    protected $table = 'direccion';
    protected $primaryKey = 'id_direccion';
    public $timestamps = false;

    protected $fillable = [
        'id_calle',
        'numero_exterior',
        'numero_interior',
    ];

    public function calle(): BelongsTo
    {
        return $this->belongsTo(Calle::class, 'id_calle', 'id_calle');
    }

    public function usuarios(): HasMany
    {
        return $this->hasMany(Usuario::class, 'id_direccion', 'id_direccion');
    }

    public function sucursales(): HasMany
    {
        return $this->hasMany(Sucursal::class, 'id_direccion', 'id_direccion');
    }
}

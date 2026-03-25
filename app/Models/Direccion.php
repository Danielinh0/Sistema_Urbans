<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Direccion extends Model
{
    use HasFactory;

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

    public function user(): HasMany
    {
        return $this->hasMany(User::class, 'id_direccion', 'id_direccion');
    }

    public function sucursales(): HasMany
    {
        return $this->hasMany(Sucursal::class, 'id_direccion', 'id_direccion');
    }
}

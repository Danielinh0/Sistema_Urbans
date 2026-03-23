<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Calle extends Model
{
    protected $table = 'calle';
    protected $primaryKey = 'id_calle';
    public $timestamps = false;

    protected $fillable = [
        'id_colonia',
        'nombre',
    ];

    public function colonia(): BelongsTo
    {
        return $this->belongsTo(Colonia::class, 'id_colonia', 'id_colonia');
    }

    public function direcciones(): HasMany
    {
        return $this->hasMany(Direccion::class, 'id_calle', 'id_calle');
    }
}

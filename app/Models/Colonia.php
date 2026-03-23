<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Colonia extends Model
{
    protected $table = 'colonia';
    protected $primaryKey = 'id_colonia';
    public $timestamps = false;

    protected $fillable = [
        'id_cp',
        'nombre',
    ];

    public function codigoPostal(): BelongsTo
    {
        return $this->belongsTo(CodigoPostal::class, 'id_cp', 'id_cp');
    }

    public function calles(): HasMany
    {
        return $this->hasMany(Calle::class, 'id_colonia', 'id_colonia');
    }
}

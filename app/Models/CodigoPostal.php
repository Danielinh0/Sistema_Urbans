<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CodigoPostal extends Model
{
    use HasFactory;

    protected $table = 'codigo_postal';
    protected $primaryKey = 'id_cp';
    public $timestamps = false;

    protected $fillable = [
        'id_estado',
        'numero',
    ];

    public function estado(): BelongsTo
    {
        return $this->belongsTo(Estado::class, 'id_estado', 'id_estado');
    }

    public function colonias(): HasMany
    {
        return $this->hasMany(Colonia::class, 'id_cp', 'id_cp');
    }
}

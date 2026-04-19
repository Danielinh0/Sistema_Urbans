<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Corrida extends Model
{
    use HasFactory;
    protected $table = 'corrida';
    protected $primaryKey = 'id_corrida';
    public $timestamps = false;

    protected $fillable = [
        'id_ruta',
        'id_usuario',
        'fecha',
        'hora_salida',
        'hora_llegada',
        'id_urban',
    ];

    protected function hora_salida(): Attribute
    {
    return Attribute::make(
        get: fn ($value) => $value ? substr($value, 0, 5) : null, // "08:30:00" → "08:30"
        set: fn ($value) => $value . ':00', // "08:30" → "08:30:00"
    );
    }

    protected function hora_llegada(): Attribute
    {
    return Attribute::make(
        get: fn ($value) => $value ? substr($value, 0, 5) : null, // "08:30:00" → "08:30"
        set: fn ($value) => $value . ':00', // "08:30" → "08:30:00"
    );
    }

    public function ruta(): BelongsTo
    {
        return $this->belongsTo(Ruta::class, 'id_ruta', 'id_ruta');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_usuario','id_usuario');
    }

    public function boletos(): HasMany
    {
        return $this->hasMany(Boleto::class, 'id_corrida', 'id_corrida');
    }

    public function urban(): BelongsTo
    {
        return $this->belongsTo(Urban::class, 'id_urban', 'id_urban');
    }
}

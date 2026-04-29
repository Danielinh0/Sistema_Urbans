<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Carbon\Carbon;

class Corrida extends Model
{
    use HasFactory;
    protected $table = 'corrida';
    protected $primaryKey = 'id_corrida';
    public $timestamps = false;

    protected $fillable = [
        'id_ruta',
        'id_usuario',
        'datetime_salida',
        'datetime_llegada',
        'estado',
        'id_urban',
    ];

    protected $casts = [
        'datetime_salida' => 'datetime',
        'datetime_llegada' => 'datetime',
    ];

    public function getFechaAttribute()
    {
        return $this->datetime_salida;
    }

    public function getHoraSalidaAttribute()
    {
        return $this->datetime_salida;
    }

    public function getHoraLlegadaAttribute()
    {
        return $this->datetime_llegada;
    }
    
    public function ruta(): BelongsTo
    {
        return $this->belongsTo(Ruta::class, 'id_ruta', 'id_ruta');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_usuario', 'id_usuario');
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

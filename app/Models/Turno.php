<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Turno extends Model
{
    protected $table = 'turno';
    protected $primaryKey = 'id_turno';

    protected $fillable = [
        'id_cajero',
        'id_taquilla',
        'id_venta',
        'monto_inicial',
        'monto_final',
        'fecha',
        'hora_inicio',
        'hora_fin',
    ];

    protected $casts = [
        'fecha' => 'date',
        'hora_inicio' => 'datetime:H:i:s',
        'hora_fin' => 'datetime:H:i:s',
    ];

    public function cajero(): BelongsTo
    {
        return $this->belongsTo(Cajero::class, 'id_cajero', 'id_usuario');
    }

    public function taquilla(): BelongsTo
    {
        return $this->belongsTo(Taquilla::class, 'id_taquilla', 'id_taquilla');
    }

    public function venta(): BelongsTo
    {
        return $this->belongsTo(Venta::class, 'id_venta', 'id_venta');
    }

    public function boletos(): HasMany
    {
        return $this->hasMany(Boleto::class, 'id_turno', 'id_turno');
    }
}

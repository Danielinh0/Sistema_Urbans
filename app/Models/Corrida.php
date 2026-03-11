<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Corrida extends Model
{
    use HasFactory;

    protected $table = 'corridas';

    protected $fillable = [
        'Hora_salida',
        'Hora_llegada',
        'Fecha',
        'id_urban',
    ];

    public function urban(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Urban::class, 'id_urban', 'id_combi');
    }

    public function tramos(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Tramo::class, 'corrida_tramo', 'id_corrida', 'id_tramo');
    }

    public function turnos(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Turno::class, 'corrida_turno', 'id_corrida', 'id_turno');
    }

    public function boletos(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Boleto::class, 'id_corrida');
    }
}

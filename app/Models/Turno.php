<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Turno extends Model
{
    use HasFactory;

    protected $table = 'turnos';

    protected $fillable = [
        'fecha',
        'horario',
    ];

    public function corridas(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Corrida::class, 'corrida_turno', 'id_turno', 'id_corrida');
    }

    public function taquillas(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Taquilla::class, 'turno_taquilla', 'id_turno', 'id_taquilla');
    }

    public function boletos(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Boleto::class, 'turno_genera_boleto', 'id_turno', 'id_boleto');
    }
}

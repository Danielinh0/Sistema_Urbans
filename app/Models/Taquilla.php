<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Taquilla extends Model
{
    use HasFactory;

    protected $table = 'taquillas';

    protected $fillable = [
        'monto_inicial',
        'monto_final',
    ];

    public function turnos(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Turno::class, 'turno_taquilla', 'id_taquilla', 'id_turno');
    }

    public function boletos(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Boleto::class, 'id_taquilla');
    }
}

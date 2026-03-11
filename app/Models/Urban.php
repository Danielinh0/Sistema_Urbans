<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Urban extends Model
{
    use HasFactory;

    protected $table = 'urbans';
    protected $primaryKey = 'id_combi';

    protected $fillable = [
        'placa',
        'codigo_combi',
        'numero_asientos',
        'id_socio',
    ];

    public function socio(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Socio::class, 'id_socio');
    }

    public function asientos(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Asiento::class, 'id_combi', 'id_combi');
    }

    public function corridas(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Corrida::class, 'id_urban', 'id_combi');
    }
}

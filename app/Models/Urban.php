<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Urban extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'urban';
    protected $primaryKey = 'id_urban';
    public $timestamps = false;

    protected $fillable = [
        'id_socio',
        'placa',
        'codigo_urban',
        'numero_asientos',
    ];

    public function socio(): BelongsTo
    {
        return $this->belongsTo(Socio::class, 'id_socio', 'id_socio');
    }

    public function asientos(): HasMany
    {
        return $this->hasMany(Asiento::class, 'id_urban', 'id_urban');
    }

    public function corrida(): HasMany
    {
        return $this->hasMany(Corrida::class, 'id_urban', 'id_urban');
    }

    public function scopeConViajesPendientes($query)
    {
        return $query->whereHas('corrida', function ($q) {
            $q->where('hora_llegada', '>=', now());
        });
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Urban extends Model
{
    use HasFactory;
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

    public function manejadas(){
        return $this->hasMany(Manejada::class, 'id_manejada', 'id_manejada');
    }
}

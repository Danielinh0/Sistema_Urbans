<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Ruta extends Model
{
    use HasFactory;

    protected $table = 'ruta';
    protected $primaryKey = 'id_ruta';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'distancia',
        'tiempo_estimado',
        'tarifa_clientes',
        'tarifa_paquete',
    ];
    
    protected function tiempoEstimado(): Attribute
    {
    return Attribute::make(
        get: fn ($value) => $value ? substr($value, 0, 5) : null, // "08:30:00" → "08:30"
        set: fn ($value) => $value . ':00', // "08:30" → "08:30:00"
    );
    }

    public function corridas(): HasMany
    {
        return $this->hasMany(Corrida::class, 'id_ruta', 'id_ruta');
    }
}

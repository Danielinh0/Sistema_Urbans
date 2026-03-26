<?php

namespace App\Models;

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

    // protected $casts = [
    //     'distancia' => 'decimal:2',
    //     'tarifa_personas' => 'decimal:2',
    //     'tarifa_paquetes' => 'decimal:2',
    // ];

    public function corridas(): HasMany
    {
        return $this->hasMany(Corrida::class, 'id_ruta', 'id_ruta');
    }
}

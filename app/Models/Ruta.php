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
    ];

    protected $casts = [
        'distancia' => 'decimal:2',
    ];

    public function corridas(): HasMany
    {
        return $this->hasMany(Corrida::class, 'id_ruta', 'id_ruta');
    }
}

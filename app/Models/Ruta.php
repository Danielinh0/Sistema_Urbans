<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ruta extends Model
{
    use HasFactory;

    protected $table = 'rutas';

    protected $fillable = [
        'nombre',
        'distancia',
    ];

    public function tramos(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Tramo::class, 'id_ruta');
    }
}

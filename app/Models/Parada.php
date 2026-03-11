<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Parada extends Model
{
    use HasFactory;

    protected $table = 'paradas';

    protected $fillable = [
        'nombre',
        'direccion',
    ];

    public function tramosQueSale(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Tramo::class, 'id_parada_sale');
    }

    public function tramosQueLlega(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Tramo::class, 'id_parada_llega');
    }
}

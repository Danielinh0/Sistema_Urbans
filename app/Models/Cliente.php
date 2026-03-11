<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;

    protected $table = 'clientes';

    protected $fillable = [
        'nombre',
        'peso_equipaje',
    ];

    public function boletos(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Boleto::class, 'id_cliente');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tramo extends Model
{
    use HasFactory;

    protected $table = 'tramos';

    protected $fillable = [
        'tarifa_cliente',
        'tarifa_paquete',
        'id_ruta',
        'id_parada_sale',
        'id_parada_llega',
    ];

    public function ruta(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Ruta::class, 'id_ruta');
    }

    public function paradaSale(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Parada::class, 'id_parada_sale');
    }

    public function paradaLlega(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Parada::class, 'id_parada_llega');
    }

    public function corridas(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Corrida::class, 'corrida_tramo', 'id_tramo', 'id_corrida');
    }

    public function boletos(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Boleto::class, 'boleto_tramo', 'id_tramo', 'id_boleto');
    }
}

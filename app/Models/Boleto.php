<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Boleto extends Model
{
    protected $table = 'boleto';
    protected $primaryKey = 'id_boleto';

    protected $fillable = [
        'id_corrida',
        'id_turno',
        'id_cliente',
        'folio',
        'estado',
        'tipo_de_pago',
        'timestamp',
        'descuento',
    ];

    protected $casts = [
        'timestamp' => 'datetime',
        'descuento' => 'decimal:2',
    ];

    public function corrida(): BelongsTo
    {
        return $this->belongsTo(Corrida::class, 'id_corrida', 'id_corrida');
    }

    public function turno(): BelongsTo
    {
        return $this->belongsTo(Turno::class, 'id_turno', 'id_turno');
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class, 'id_cliente', 'id_cliente');
    }

    public function ventas(): HasMany
    {
        return $this->hasMany(Venta::class, 'id_boleto', 'id_boleto');
    }

    public function boletoCliente(): HasOne
    {
        return $this->hasOne(BoletoCliente::class, 'id_boleto', 'id_boleto');
    }

    public function boletoPaquete(): HasOne
    {
        return $this->hasOne(BoletoPaquete::class, 'id_boleto', 'id_boleto');
    }
}

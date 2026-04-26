<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Boleto extends Model
{
    use HasFactory;
    protected $table = 'boleto';
    protected $primaryKey = 'id_boleto';

    protected $fillable = [
        'id_corrida',
        'id_detalle_venta',
        'id_cliente',
        'folio',
        'estado',
        'tipo_de_pago',
        'descuento',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'descuento' => 'decimal:2',
    ];

    public function corrida(): BelongsTo
    {
        return $this->belongsTo(Corrida::class, 'id_corrida', 'id_corrida');
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class, 'id_cliente', 'id_cliente');
    }

    public function boletoCliente(): HasOne
    {
        return $this->hasOne(BoletoCliente::class, 'id_boleto', 'id_boleto');
    }

    public function boletoPaquete(): HasOne
    {
        return $this->hasOne(BoletoPaquete::class, 'id_boleto', 'id_boleto');
    }

    public function detalleVenta(): BelongsTo
    {
        return $this->belongsTo(DetalleVenta::class, 'id_detalle_venta', 'id_detalle_venta');
    }
}

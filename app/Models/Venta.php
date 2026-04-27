<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Venta extends Model
{
    use HasFactory;

    protected $table = 'venta';
    protected $primaryKey = 'id_venta';

    protected $fillable = [
        'id_boleto',
        'id_cliente',
        'total',
        'subtotal',
        'descuento',
        'fecha',
    ];

    protected $casts = [
        'fecha' => 'date',
    ];

    public function boleto(): BelongsTo
    {
        return $this->belongsTo(Boleto::class, 'id_boleto', 'id_boleto');
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class, 'id_cliente', 'id_cliente');
    }

    public function turnos(): HasMany
    {
        return $this->hasMany(Turno::class, 'id_venta', 'id_venta');
    }

    public function detalleVentas(): HasMany
    {
        return $this->hasMany(DetalleVenta::class, 'id_venta', 'id_venta');
    }
}

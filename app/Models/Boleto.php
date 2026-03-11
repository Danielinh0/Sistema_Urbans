<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Boleto extends Model
{
    use HasFactory;

    protected $table = 'boletos';

    protected $fillable = [
        'Total',
        'estado',
        'tipo_de_pago',
        'tipo',
        'timestamp_emision',
        'descuento',
        'folio',
        'guia',
        'id_cliente',
        'id_taquilla',
        'id_corrida',
    ];

    protected function casts(): array
    {
        return [
            'timestamp_emision' => 'datetime',
            'Total'             => 'decimal:2',
            'descuento'         => 'decimal:2',
        ];
    }

    public function cliente(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Cliente::class, 'id_cliente');
    }

    public function taquilla(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Taquilla::class, 'id_taquilla');
    }

    public function corrida(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Corrida::class, 'id_corrida');
    }

    public function boletoCliente(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(BoletoCliente::class, 'id_boleto');
    }

    public function boletoPaquete(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(BoletoPaquete::class, 'id_boleto');
    }

    public function tramos(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Tramo::class, 'boleto_tramo', 'id_boleto', 'id_tramo');
    }

    public function turnos(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Turno::class, 'turno_genera_boleto', 'id_boleto', 'id_turno');
    }
}

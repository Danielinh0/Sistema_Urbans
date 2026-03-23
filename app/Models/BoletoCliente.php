<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BoletoCliente extends Model
{
    protected $table = 'boleto_cliente';
    protected $primaryKey = 'id_boleto';
    public $timestamps = false;

    protected $fillable = [
        'id_boleto',
        'id_asiento',
        'peso_equipaje',
    ];

    protected $casts = [
        'peso_equipaje' => 'decimal:2',
    ];

    public function boleto(): BelongsTo
    {
        return $this->belongsTo(Boleto::class, 'id_boleto', 'id_boleto');
    }

    public function asiento(): BelongsTo
    {
        return $this->belongsTo(Asiento::class, 'id_asiento', 'id_asiento');
    }
}

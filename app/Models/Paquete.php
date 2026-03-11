<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paquete extends Model
{
    use HasFactory;

    protected $table = 'paquetes';

    protected $fillable = [
        'peso',
        'descripcion',
        'tipo_de_pago',
        'destinatario',
        'id_boleto',
    ];

    public function boletoPaquete(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(BoletoPaquete::class, 'id_boleto', 'id_boleto');
    }
}

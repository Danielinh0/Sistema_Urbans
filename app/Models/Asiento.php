<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asiento extends Model
{
    use HasFactory;

    protected $table = 'asientos';
    protected $primaryKey = 'id_asiento';

    protected $fillable = [
        'nombre',
        'id_combi',
    ];

    public function urban(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Urban::class, 'id_combi', 'id_combi');
    }

    public function boletosCliente(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(
            BoletoCliente::class,
            'boleto_cliente_asiento',
            'id_asiento',
            'id_boleto_cliente',
            'id_asiento',
            'id_boleto'
        );
    }
}

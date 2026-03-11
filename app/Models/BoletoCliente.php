<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BoletoCliente extends Model
{
    use HasFactory;

    protected $table = 'boleto_clientes';
    protected $primaryKey = 'id_boleto';
    public $incrementing = false;

    protected $fillable = ['id_boleto'];

    public function boleto(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Boleto::class, 'id_boleto');
    }

    public function asientos(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(
            Asiento::class,
            'boleto_cliente_asiento',
            'id_boleto_cliente',
            'id_asiento',
            'id_boleto',
            'id_asiento'
        );
    }
}

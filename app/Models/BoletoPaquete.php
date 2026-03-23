<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;  

class BoletoPaquete extends Model
{
    use HasFactory;

    protected $table = 'boleto_paquete';
    protected $primaryKey = 'id_boleto';
    public $timestamps = false;

    protected $fillable = [
        'id_boleto',
        'guia',
        'descripcion',
        'peso',
        'destinatario',
    ];

    protected $casts = [
        'peso' => 'decimal:2',
    ];

    public function boleto(): BelongsTo
    {
        return $this->belongsTo(Boleto::class, 'id_boleto', 'id_boleto');
    }
}

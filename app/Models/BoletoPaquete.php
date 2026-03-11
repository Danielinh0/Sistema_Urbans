<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BoletoPaquete extends Model
{
    use HasFactory;

    protected $table = 'boleto_paquetes';
    protected $primaryKey = 'id_boleto';
    public $incrementing = false;

    protected $fillable = ['id_boleto'];

    public function boleto(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Boleto::class, 'id_boleto');
    }

    public function paquete(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Paquete::class, 'id_boleto', 'id_boleto');
    }
}

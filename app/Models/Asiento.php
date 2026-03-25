<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Asiento extends Model
{
    use HasFactory;
    
    protected $table = 'asiento';
    protected $primaryKey = 'id_asiento';
    public $timestamps = false;

    protected $fillable = [
        'id_urban',
        'nombre',
    ];

    public function urban(): BelongsTo
    {
        return $this->belongsTo(Urban::class, 'id_urban', 'id_urban');
    }

    public function boletoClientes(): HasMany
    {
        return $this->hasMany(BoletoCliente::class, 'id_asiento', 'id_asiento');
    }
}

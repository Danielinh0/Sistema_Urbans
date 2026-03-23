<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use     Illuminate\Database\Eloquent\Factories\HasFactory;

class Taquilla extends Model
{
    use HasFactory;
    
    protected $table = 'taquilla';
    protected $primaryKey = 'id_taquilla';
    public $timestamps = false;

    protected $fillable = [
        'monto_actual',
    ];

    public function turnos(): HasMany
    {
        return $this->hasMany(Turno::class, 'id_taquilla', 'id_taquilla');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Socio extends Model
{
    use HasFactory;

    protected $table = 'socios';

    protected $fillable = [
        'numero_telefonico',
        'correo',
    ];

    public function urbans(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Urban::class, 'id_socio');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Socio extends Model
{
    protected $table = 'socio';
    protected $primaryKey = 'id_socio';
    public $timestamps = false;

    protected $fillable = [
        'numero_telefonico',
        'correo',
    ];

    public function urbans(): HasMany
    {
        return $this->hasMany(Urban::class, 'id_socio', 'id_socio');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cajero extends Usuario
{
    protected $table = 'cajero';
    protected $primaryKey = 'id_usuario';

    protected $fillable = [
        'id_usuario',
    ];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario');
    }

    public function turnos(): HasMany
    {
        return $this->hasMany(Turno::class, 'id_cajero', 'id_usuario');
    }
}

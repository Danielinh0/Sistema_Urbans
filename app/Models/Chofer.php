<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Chofer extends Usuario
{
    protected $table = 'chofer';
    protected $primaryKey = 'id_usuario';

    protected $fillable = [
        'id_usuario',
    ];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario');
    }

    public function corridas(): HasMany
    {
        return $this->hasMany(Corrida::class, 'id_chofer', 'id_usuario');
    }
}

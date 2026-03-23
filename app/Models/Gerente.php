<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Gerente extends Usuario
{
    protected $table = 'gerente';
    protected $primaryKey = 'id_usuario';

    protected $fillable = [
        'id_usuario',
    ];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario');
    }
}

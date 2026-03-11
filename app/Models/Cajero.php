<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cajero extends Model
{
    use HasFactory;

    protected $table = 'cajeros';
    protected $primaryKey = 'id_usuario';
    public $incrementing = false;

    protected $fillable = ['id_usuario'];

    public function usuario(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'id_usuario');
    }
}

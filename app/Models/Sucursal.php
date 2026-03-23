<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Sucursal extends Model
{
    use HasFactory;

    protected $table = 'sucursal';
    protected $primaryKey = 'id_sucursal';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'ubicacion',
        'id_direccion',
    ];

    public function direccion(): BelongsTo
    {
        return $this->belongsTo(Direccion::class, 'id_direccion', 'id_direccion');
    }

    public function usuarios(): HasMany
    {
        return $this->hasMany(Usuario::class, 'id_sucursal', 'id_sucursal');
    }
}

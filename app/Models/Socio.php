<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Socio extends Model
{
    use HasFactory;

    protected $table = 'socio';
    protected $primaryKey = 'id_socio';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'apellido_paterno',
        'apellido_materno',
        'estado',
        'fecha_de_incorporacion',
        'numero_telefonico',
        'correo',
    ];

    public function urbans(): HasMany
    {
        return $this->hasMany(Urban::class, 'id_socio', 'id_socio');
    }
}

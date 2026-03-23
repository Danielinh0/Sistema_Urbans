<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pais extends Model
{
    use HasFactory;

    protected $table = 'pais';
    protected $primaryKey = 'id_pais';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
    ];

    public function estados(): HasMany
    {
        return $this->hasMany(Estado::class, 'id_pais', 'id_pais');
    }
}

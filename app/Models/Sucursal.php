<?php

namespace App\Models;

use App\Models\Direccion; 
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
        'id_direccion',
    ];

    public function direccion(): BelongsTo
    {
        return $this->belongsTo(Direccion::class, 'id_direccion', 'id_direccion');
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'id_sucursal', 'id_sucursal');
    }

    public function rutas(){
        return $this->hasMany(Ruta::class, 'id_sucursal_salida', 'id_sucursal')
                    ->orWhere('id_sucursal_llegada', $this->id_sucursal);
    }

    protected static function booted(): void
    {
        static::deleted(function (Sucursal $sucursal) {
            if (!$sucursal->id_direccion) {
                return;
            }

            $direccion = Direccion::find($sucursal->id_direccion);

            if (!$direccion) {
                return;
            }

            if (!$direccion->sucursales()->exists()) {
                $direccion->delete();
            }
        });
    }
}

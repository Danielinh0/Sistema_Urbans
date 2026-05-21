<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Prediccion extends Model
{
    protected $table = 'predicciones';

    protected $primaryKey = 'id_prediccion';

    protected $fillable = [
        'id_ruta',
        'id_urban',
        'fecha_salida',
        'hora_salida',
        'dia_semana',
        'mes',
        'dia_mes',
        'es_festivo',
        'es_finde',
        'boletos_estimados',
        'modelo_version',
        'r2_modelo',
        'id_usuario',
    ];

    protected $casts = [
        'fecha_salida' => 'date',
        'hora_salida' => 'datetime:H:i',
        'es_festivo' => 'boolean',
        'es_finde' => 'boolean',
    ];

    public function ruta(): BelongsTo
    {
        return $this->belongsTo(Ruta::class, 'id_ruta', 'id_ruta');
    }

    public function urban(): BelongsTo
    {
        return $this->belongsTo(Urban::class, 'id_urban', 'id_urban');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_usuario', 'id_usuario');
    }
}

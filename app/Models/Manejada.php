<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Manejada extends Model
{
    use HasFactory;
    protected $table = 'manejada';
    protected $primaryKey = 'id_manejada';
    protected $fillable = [
        'fecha',
        'id_usuario',
        'id_urban',  
    ];

    public function usuarios(){
        return $this->belongsTo(User::class, 'id_usuario', 'id_usuario');
    }
    public function urbans(){
        return $this->belongsTo(Urban::class, 'id_urban', 'id_urban');
    }

    public function corridas(){
        return $this->belongsToMany(Corrida::class, 'manejada_corrida', 'id_manejada', 'id_corrida');
    }
}

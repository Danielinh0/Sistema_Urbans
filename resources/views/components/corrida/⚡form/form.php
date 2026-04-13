<?php

use Livewire\Attributes\Computed;
use Livewire\Component;
use App\Models\Ruta;
use App\Models\User;

new class extends Component
{   

    public $id_ruta;
    public $id_usuario;

    #[Computed]
    public function rutas(){
        return Ruta::orderBy('id_ruta')->get();
    }

    #[Computed]
    public function usuarios(){
        return User::orderBy('id_usuario')->get();
    }

};

<?php

use Livewire\Component; 
use App\Models\Ruta; 

new class extends Component
{
    public $RutaId;
    // public $tipo = 'editar';

    public $bg = 'azul_menu';
    public $c_text = 'white';
    public $icon = 'map-pin-pen';
    public $text = 'Editar';

    public function update(){
        $ruta = Ruta::findOrFail($this->RutaId);
    }

    public function delete(){

    }
};  
?>

<div>
    <flux:button class="!bg-{{ $bg }} !text-{{ $c_text }} 
    transition delay-150 duration-300 ease-in-out hover:-translate-y-1 hover:scale-100 
    hover:bg-{{ $bg }}/110" icon="{{ $icon }}"> {{ $text }} </flux:button>

</div>
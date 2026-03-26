<?php

use Livewire\Component;
use App\Models\Ruta;

new class extends Component
{
    public $nombre;
    public $distancia;
    public $tiempo_estimado;
    public $tarifa_clientes;
    public $tarifa_paquete; 

    public function save()
    {
        Ruta::create([
            'nombre' => $this->nombre,
            'distancia' => $this->distancia,
            'tiempo_estimado' => $this->tiempo_estimado,
            'tarifa_clientes' => $this->tarifa_clientes,
            'tarifa_paquete' => $this->tarifa_paquete,
        ]);

        Flux::toast('Your changes have been saved.');
    }
};
?>

<div>
    <form wire:submit="save">
    <flux:card class="space-y-6">
        <div>
            <flux:heading class="!text-2xl !font-bold" size="lg">Crea una nueva ruta</flux:heading>        
        </div>
        <div class="space-y-6">
            <flux:input wire:model="nombre" icon:trailing="a-large-small" type="text" label="Nombre de la ruta"  description:trailing="No se debe dejar en blanco, siendo de almenos 20 caracteres manejandose por la nomenclatura 'Ruta - Destino'"/>
            <flux:input wire:model="distancia" icon:trailing="land-plot"  label="Distancia"  description:trailing="Ingrese la distancia de la ruta en kilómetros"/>
            <flux:input wire:model="tiempo_estimado" icon:trailing="clock-fading" label="Tiempo Estimado de Viaje"  description:trailing="Tiempo estimado para completar la ruta en formato HH:MM"/>
            <flux:input wire:model="tarifa_clientes " icon:trailing="book-user" label="Tarifa para personas"  description:trailing="Ingrese la tarifa para personas en la ruta "/>
            <flux:input wire:model="tarifa_paquete" icon:trailing="package" label="Tarifa para paquetes"  description:trailing="Ingrese la tarifa para paquetes en la ruta "/>
            
        </div>
        <div class="space-y-2">
            <flux:button type="submit" variant="primary" class="w-full">Crear Ruta</flux:button>
            
        </div>
    </flux:card> 
</form>
</div>
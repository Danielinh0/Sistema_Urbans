<?php

use Livewire\{Component, WithPagination};
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;
use App\Models\Ruta;

new class extends Component
{
    use WithPagination;

    #[Validate('required|min:3')]
    public $nombre;

    #[Validate('required|numeric|min:0')]
    public $distancia;

    #[Validate('required|date_format:H:i')]
    public $tiempo_estimado;

    #[Validate('required|numeric|min:0')]
    public $tarifa_clientes;

    #[Validate('required|numeric|min:0')]
    public $tarifa_paquete; 

    public $sortBy = 'id_ruta';
    public $sortDirection = 'asc';

    public function sort($column) {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }
    }

    #[Computed]
    public function rutas()
    {
        return Ruta::query()
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate(10);
    }

    public function save()
    {
        $this->validate();

        Ruta::create([
            'nombre' => $this->nombre,
            'distancia' => $this->distancia,
            'tiempo_estimado' => $this->tiempo_estimado,
            'tarifa_clientes' => $this->tarifa_clientes,
            'tarifa_paquete' => $this->tarifa_paquete,
        ]);

        $this->reset(['nombre', 'distancia', 'tiempo_estimado', 'tarifa_clientes', 'tarifa_paquete']);

        Flux::toast('Your changes have been saved.');
    }
};
?>

<div class="flex flex-col md:flex-row gap-6">
    <div class="w-full md:w-1/3">
        <form wire:submit="save">
            <flux:card class="space-y-6">
                <div>
                    <flux:heading class="!text-2xl !font-bold" size="lg">Crea una nueva ruta</flux:heading>        
                </div>
                <div class="space-y-6">
                    <div>
                        <flux:input wire:model="nombre" icon:trailing="a-large-small" type="text" label="Nombre de la ruta"  description:trailing="No se debe dejar en blanco, siendo de almenos 20 caracteres manejandose por la nomenclatura 'Ruta - Destino'"/>
                        @error('nombre') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <flux:input wire:model="distancia" icon:trailing="land-plot"  label="Distancia"  description:trailing="Ingrese la distancia de la ruta en kilómetros"/>
                        @error('distancia') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <flux:input wire:model="tiempo_estimado" icon:trailing="clock-fading" label="Tiempo Estimado de Viaje"  description:trailing="Tiempo estimado para completar la ruta en formato HH:MM"/>
                        @error('tiempo_estimado') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <flux:input wire:model="tarifa_clientes" icon:trailing="book-user" label="Tarifa para personas"  description:trailing="Ingrese la tarifa para personas en la ruta "/>
                        @error('tarifa_clientes') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <flux:input wire:model="tarifa_paquete" icon:trailing="package" label="Tarifa para paquetes"  description:trailing="Ingrese la tarifa para paquetes en la ruta "/>
                        @error('tarifa_paquete') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="space-y-2">
                    <flux:button type="submit" variant="primary" class="w-full">Crear Ruta</flux:button>
                </div>
            </flux:card> 
        </form>
    </div>

    <div class="w-full md:w-2/3">
        <flux:card>
            <flux:table :paginate="$this->rutas">
        <flux:table.columns>
            <flux:table.column sortable :sorted="$sortBy === 'id_ruta'" :direction="$sortDirection" wire:click="sort('id_ruta')">ID</flux:table.column>
            <flux:table.column sortable :sorted="$sortBy === 'nombre'" :direction="$sortDirection" wire:click="sort('nombre')">Nombre de Ruta</flux:table.column>
            <flux:table.column sortable :sorted="$sortBy === 'distancia'" :direction="$sortDirection" wire:click="sort('distancia')">Distancia (km)</flux:table.column>
            <flux:table.column sortable :sorted="$sortBy === 'tiempo_estimado'" :direction="$sortDirection" wire:click="sort('tiempo_estimado')">Tiempo Est.</flux:table.column>
            <flux:table.column sortable :sorted="$sortBy === 'tarifa_clientes'" :direction="$sortDirection" wire:click="sort('tarifa_clientes')">Tarifa Personas</flux:table.column>
            <flux:table.column sortable :sorted="$sortBy === 'tarifa_paquete'" :direction="$sortDirection" wire:click="sort('tarifa_paquete')">Tarifa Paquetes</flux:table.column>
            <flux:table.column>Acciones</flux:table.column>
        </flux:table.columns>
        <flux:table.rows>
            @forelse ($this->rutas as $ruta)
                <flux:table.row :key="$ruta->id_ruta">
                    <flux:table.cell class="flex items-center gap-3">
                        {{ $ruta->id_ruta }}
                    </flux:table.cell>
                    <flux:table.cell class="whitespace-nowrap">
                        {{ $ruta->nombre }}
                    </flux:table.cell>
                    <flux:table.cell>
                        {{ $ruta->distancia }} km
                    </flux:table.cell>
                    <flux:table.cell>
                        {{ $ruta->tiempo_estimado }}
                    </flux:table.cell>
                    <flux:table.cell variant="strong">
                        ${{ number_format($ruta->tarifa_clientes, 2) }}
                    </flux:table.cell>
                    <flux:table.cell variant="strong">
                        ${{ number_format($ruta->tarifa_paquete, 2) }}
                    </flux:table.cell>
                    <flux:table.cell>
                        <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" inset="top bottom" />
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="7" class="text-center py-4">
                        No se encontraron rutas.
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>
        </flux:card>
    </div>
</div>
<?php

use Livewire\WithPagination;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use App\Models\Ruta;

new class extends Component
{
    use WithPagination;
    
    public $sortBy = 'id_ruta';
    public $sortDirection = 'desc';
    public $search = '';

    public function sort($column) {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }
    }

    #[On('searchUpdated')]
    public function updateSearch($value)
    {
        $this->search = $value;
        $this->resetPage();
    }

    #[On('ruta-creada')]
    public function refreshAfterCreate()
    {
        $this->resetPage();
    }

    #[Computed]
    public function rutas()
    {
        return Ruta::query()
            ->when($this->search !== '', function ($query) {
                $query->whereRaw('LOWER(nombre) like ?', ['%'.strtolower($this->search).'%']);
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate(10);
    }

};
?>

<div>
    <div>
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
                        <flux:button class="!bg-azul_menu !text-white transition delay-150 duration-300 ease-in-out hover:-translate-y-1 hover:scale-100 hover:bg-azul_menu/110" icon="map-pin-pen">Editar</flux:button>
                        <flux:button class="!bg-rojo_boton !text-rojo_texto transition delay-150 duration-300 ease-in-out hover:-translate-y-1 hover:scale-100 hover:bg-rojo_boton/110" icon="map-pin-x">Eliminar</flux:button>
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="7" class="text-center py-4 ">
                        No se encontraron rutas.
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>
        </flux:card>
    </div>
</div>
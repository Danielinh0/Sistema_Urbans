<?php

use Livewire\WithPagination;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use App\Models\Sucursal;

new class extends Component
{
    use WithPagination;
    
    public $sortBy = 'id_sucursal';
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

    #[On('sucursal-creada')]
    public function refreshAfterCreate()
    {
        $this->resetPage();
    }

    #[Computed]
    public function sucursales()
    {
        return Sucursal::query()
            ->with(['direccion.calle.colonia.codigoPostal.estado.pais'])
            ->when($this->search !== '', function ($query) {
                $query->whereRaw('LOWER(nombre) like ?', ['%'.strtolower($this->search).'%']);
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate(10);
    }
};
?>

<div>
    <div>rutas
        <flux:card>
        <flux:table :paginate="$this->sucursales">
            <flux:table.columns>
                <flux:table.column sortable :sorted="$sortBy === 'id_sucursal'" :direction="$sortDirection" wire:click="sort('id_sucursal')">ID</flux:table.column>
                <flux:table.column sortable :sorted="$sortBy === 'nombre'" :direction="$sortDirection" wire:click="sort('nombre')">Nombre de la Sucursal</flux:table.column>
                <flux:table.column sortable :sorted="$sortBy === 'direccion'" :direction="$sortDirection" wire:click="sort('direccion')">Direccion</flux:table.column>
                <flux:table.column>Acciones</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
            @forelse ($this->sucursales as $sucursal)
                <flux:table.row :key="$sucursal->id_sucursal">
                    <flux:table.cell class="flex items-center gap-3">
                        {{ $sucursal->id_sucursal }}
                    </flux:table.cell>
                    <flux:table.cell class="whitespace-nowrap">
                        {{ $sucursal->nombre }}
                    </flux:table.cell>
                    <flux:table.cell>
                        {{$sucursal->direccion->calle->nombre ?? ''}}
                        #{{$sucursal->direccion->numero_exterior ?? ''}}, 
                        {{$sucursal->direccion->calle->colonia->nombre ?? ''}}, 
                        {{$sucursal->direccion->calle->colonia->codigoPostal->numero ?? ''}}, 
                        {{$sucursal->direccion->calle->colonia->codigoPostal->estado->nombre ?? ''}},
                        {{$sucursal->direccion->calle->colonia->codigoPostal->estado->pais->nombre ?? ''}}
                    </flux:table.cell>
                    <flux:table.cell>
                        <flux:button class="!bg-azul_menu !text-white transition delay-150 duration-300 ease-in-out hover:-translate-y-1 hover:scale-100 hover:bg-azul_menu/110" icon="map-pin-pen">Editar</flux:button>
                        <flux:button class="!bg-rojo_boton Eager Loading!text-rojo_texto transition delay-150 duration-300 ease-in-out hover:-translate-y-1 hover:scale-100 hover:bg-rojo_boton/110" icon="map-pin-x">Eliminar</flux:button>
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="7" class="text-center py-4 ">
                        No se encontraron sucursales.
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
        </flux:table>
        </flux:card>
    </div>
</div>
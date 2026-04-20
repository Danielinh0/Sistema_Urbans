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
    #[On('sucursal-eliminada')]
    #[On('sucursal-actualizada')]
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
    <livewire:barra-busqueda placeholder="Buscar una Sucursal" />
    <div>
        <flux:card>
            <flux:table :paginate="$this->sucursales">
                <flux:table.columns>
                    <flux:table.column sortable :sorted="$sortBy === 'id_sucursal'" :direction="$sortDirection" wire:click="sort('id_sucursal')">ID</flux:table.column>
                    <flux:table.column sortable :sorted="$sortBy === 'nombre'" :direction="$sortDirection" wire:click="sort('nombre')">Nombre de la Sucursal</flux:table.column>
                    <flux:table.column >Direccion</flux:table.column>
                    <flux:table.column>Acciones</flux:table.column>
                </flux:table.columns>
                <flux:table.rows>
                @forelse ($this->sucursales as $sucursal)
                    <flux:table.row :key="$sucursal->id_sucursal">
                        <flux:table.cell >
                            {{ $sucursal->id_sucursal }}
                        </flux:table.cell>
                        <flux:table.cell >
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
                        <flux:table.cell class="flex gap-2">
                            <flux:button variant="ghost" icon="pencil" class="!text-azul_menu"
                                wire:click="$dispatch('preparar-edicion-sucursal', { id: {{ $sucursal->id_sucursal }} })">
                                Editar
                            </flux:button>
                            @if (!$sucursal->users->count()>0)
                                <flux:button variant="ghost" icon="trash" class="!text-rojo_texto"
                                    wire:click="$dispatch('preparar-eliminacion-sucursal', { id: {{ $sucursal->id_sucursal }} })">
                                    Eliminar
                                </flux:button>
                            @endif
                            
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
        <livewire:sucursal.manager></livewire:sucursal.manager>
    </div>
</div>
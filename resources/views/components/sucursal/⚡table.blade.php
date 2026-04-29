<?php

use Livewire\WithPagination;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use App\Models\Sucursal;

new class extends Component {
    use WithPagination;

    public $sortBy = 'id_sucursal';
    public $sortDirection = 'asc';
    public $search = '';
    public $perPage = 7;

    public function sort($column)
    {
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
                $query->whereRaw('LOWER(nombre) like ?', ['%' . strtolower($this->search) . '%']);
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);
    }
};
?>

<div>
    <div>
        <livewire:barra-busqueda placeholder="Buscar por nombre de sucursal" />
        <flux:card class="!p-2 overflow-x-auto">
            <flux:table :paginate="$this->sucursales" class="w-full text-sm compact-table" dense>
                <flux:table.columns>
                    <x-header-table sortable="id_sucursal" class="w-[3.25rem] col-hide-sm" :sortBy="$sortBy" :sortDirection="$sortDirection">ID</x-header-table>

                    <x-header-table icon="book-a" sortable="nombre" class="w-[11rem]" :sortBy="$sortBy" :sortDirection="$sortDirection">Nombre</x-header-table>

                    <x-header-table icon="map-pin-house">Dirección</x-header-table>

                    @if(auth()->user()->hasAnyRole(['admin', 'gerente']))
                    <x-header-table icon="wrench" class="w-[10rem]" align="center">Acciones</x-header-table>
                    @endif
                </flux:table.columns>

                <flux:table.rows>
                    @forelse ($this->sucursales as $sucursal)
                    <flux:table.row :key="$sucursal->id_sucursal">
                        <flux:table.cell class="!px-2 w-[3.25rem] text-center tabular-nums col-hide-sm">
                            {{ $sucursal->id_sucursal }}
                        </flux:table.cell>
                        <flux:table.cell class="!px-2">
                            {{ $sucursal->nombre }}
                        </flux:table.cell>
                        <flux:table.cell class="!px-2">
                            {{ ($sucursal->direccion->calle->nombre ?? '') . ' #' . ($sucursal->direccion->numero_exterior ?? '') . ', ' . ($sucursal->direccion->calle->colonia->nombre ?? '') }}
                        </flux:table.cell>
                        @can('update', $sucursal)
                        <flux:table.cell class="flex gap-1 justify-end !px-2 whitespace-nowrap">
                            <flux:button size="sm" variant="ghost" icon="pencil" class="!text-azul_menu"
                                wire:click="$dispatch('preparar-edicion-sucursal', { id: {{ $sucursal->id_sucursal }} })">
                                Editar
                            </flux:button>
                            @if (!$sucursal->users->count() > 0)
                            <flux:button size="sm" variant="ghost" icon="trash" class="!text-rojo_texto"
                                wire:click="$dispatch('preparar-eliminacion-sucursal', { id: {{ $sucursal->id_sucursal }} })">
                                Eliminar
                            </flux:button>
                            @endif
                        </flux:table.cell>
                        @endcan
                    </flux:table.row>
                    @empty
                    <flux:table.row>
                        <flux:table.cell colspan="4" class="text-center py-4">
                            No se encontraron sucursales.
                        </flux:table.cell>
                    </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>

            <flux:select size="sm" class="w-full sm:w-auto" wire:model.live="perPage">
                <flux:select.option value="7">7</flux:select.option>
                <flux:select.option value="14">14</flux:select.option>
                <flux:select.option value="27">27</flux:select.option>
                <flux:select.option value="48">48</flux:select.option>
            </flux:select>

        </flux:card>
        <livewire:sucursal.manager></livewire:sucursal.manager>
    </div>
</div>
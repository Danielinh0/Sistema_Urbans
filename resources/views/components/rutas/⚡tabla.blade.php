<?php

use Livewire\WithPagination;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use App\Models\Ruta;

new class extends Component {
    use WithPagination;

    public $sortBy = 'id_ruta';
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

    #[On('ruta-eliminada')]
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
                $query->whereRaw('LOWER(nombre) like ?', ['%' . strtolower($this->search) . '%']);
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);
    }
};
?>

<div>

    <livewire:barra-busqueda placeholder="Buscar por nombre de ruta" />

    <flux:card class="!p-2 overflow-x-auto">
        <flux:table :paginate="$this->rutas" class="w-full text-sm compact-table" dense>
            <flux:table.columns>

                <x-header-table sortable="id_ruta" class="w-[3.25rem] col-hide-sm" :sortBy="$sortBy" :sortDirection="$sortDirection">ID</x-header-table>

                <x-header-table icon="map-pinned" class="w-[11rem]">Ruta</x-header-table>

                <x-header-table icon="land-plot" class="w-[5rem] col-hide-md">Dist. (km)</x-header-table>

                <x-header-table icon="alarm-clock" sortable="tiempo_estimado" class="w-[5rem]" :sortBy="$sortBy" :sortDirection="$sortDirection">Tiempo</x-header-table>

                <x-header-table icon="tickets" sortable="tarifa_clientes" class="w-[6rem] col-hide-md" :sortBy="$sortBy" :sortDirection="$sortDirection">Tarifa</x-header-table>

                <x-header-table icon="package" sortable="tarifa_paquete" class="w-[6rem] col-hide-md" :sortBy="$sortBy" :sortDirection="$sortDirection">Paquetes</x-header-table>

                <x-header-table icon="wrench" class="w-[10rem]" align="center">Acciones</x-header-table>
            </flux:table.columns>
            <flux:table.rows>
                @forelse ($this->rutas as $ruta)
                    <flux:table.row :key="$ruta->id_ruta">
                        <flux:table.cell class="!px-1 w-[3.25rem] text-center tabular-nums col-hide-sm">
                            {{ $ruta->id_ruta }}
                        </flux:table.cell>

                        <flux:table.cell class="!px-2 w-[11rem]">
                            <div class="truncate" title="{{ $ruta->nombre }}">
                                {{ Str::limit($ruta->nombre, 25, '...') }}
                            </div>
                        </flux:table.cell>

                        <flux:table.cell class="!px-2 w-[5rem] text-center col-hide-md">
                            {{ $ruta->distancia }}
                        </flux:table.cell>

                        <flux:table.cell class="!px-2 w-[5rem]">
                            {{ $ruta->tiempo_estimado }}
                        </flux:table.cell>

                        <flux:table.cell class="!px-2 w-[6rem] col-hide-md">
                            <flux:badge color="green" size="sm">${{ number_format($ruta->tarifa_clientes, 2) }}</flux:badge>
                        </flux:table.cell>

                        <flux:table.cell class="!px-2 w-[6rem] col-hide-md">
                            <flux:badge color="green" size="sm">${{ number_format($ruta->tarifa_paquete, 2) }}</flux:badge>
                        </flux:table.cell>

                        <flux:table.cell class="!px-2 w-[10rem]">
                            <div class="flex items-center justify-end gap-1 whitespace-nowrap">
                                @can('update', $ruta)
                                    <flux:button size="sm" variant="ghost" icon="pencil" class="!text-azul_menu"
                                        wire:click="$dispatch('edicion-ruta', { id: {{ $ruta->id_ruta }} })">
                                        Editar
                                    </flux:button>
                                @endcan

                                @can('delete', $ruta)
                                    <flux:button size="sm" variant="ghost" icon="trash" class="!text-rojo_texto"
                                        wire:click="$dispatch('eliminacion-ruta', { id: {{ $ruta->id_ruta }} })">
                                        Eliminar
                                    </flux:button>
                                @endcan
                            </div>
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
        <flux:select size="sm" class="w-full sm:w-auto" wire:model.live="perPage">
            <flux:select.option value="7">7</flux:select.option>
            <flux:select.option value="14">14</flux:select.option>
            <flux:select.option value="27">27</flux:select.option>
            <flux:select.option value="48">48</flux:select.option>
        </flux:select>
    </flux:card>



    <livewire:rutas.modal />
</div>
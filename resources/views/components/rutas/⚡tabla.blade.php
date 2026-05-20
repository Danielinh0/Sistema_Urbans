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

<div class="py-2"> {{-- quitado px-4 --}}

    <livewire:barra-busqueda placeholder="Buscar por nombre de ruta" />

    <flux:card> 
        <flux:table :paginate="$this->rutas" dense>  {{-- quitado class="w-full text-sm compact-table" --}}
            <flux:table.columns>

                <flux:table.column align="center">
                    <span class="inline-flex items-center gap-1 text-azul_menu text-sm font-semibold">
                        <flux:icon name="map-pinned" class="text-azul_menu!" /> Ruta
                    </span>
                </flux:table.column>

                <flux:table.column>
                    <span class="inline-flex items-center gap-1 text-azul_menu text-sm font-semibold">
                        <flux:icon name="map-pinned" class="text-azul_menu!" /> Salida
                    </span>
                </flux:table.column>

                <flux:table.column>
                    <span class="inline-flex items-center gap-1 text-azul_menu text-sm font-semibold">
                        <flux:icon name="map-pinned" class="text-azul_menu!" /> Llegada
                    </span>
                </flux:table.column>

                <flux:table.column align="center">
                    <span class="inline-flex items-center gap-1 text-azul_menu text-sm font-semibold">
                        <flux:icon name="land-plot" class="text-azul_menu!" /> Dist. (km)
                    </span>
                </flux:table.column>

                <flux:table.column  align="center"
                    sortable
                    :sorted="$sortBy === 'tiempo_estimado'"
                    :direction="$sortDirection"
                    wire:click="sort('tiempo_estimado')"

                >
                    <span class="inline-flex items-center gap-1 text-azul_menu text-sm font-semibold">
                        <flux:icon name="alarm-clock" class="text-azul_menu!" /> Tiempo
                    </span>
                </flux:table.column>

                <flux:table.column align="center">
                    <span class="inline-flex items-center gap-1 text-azul_menu text-sm font-semibold">
                        <flux:icon name="tickets" class="text-azul_menu!" /> Tarifa
                    </span>
                </flux:table.column>

                <flux:table.column align="center">
                    <span class="inline-flex items-center gap-1 text-azul_menu text-sm font-semibold">
                        <flux:icon name="package" class="text-azul_menu!" /> Paquetes
                    </span>
                </flux:table.column>


                @if (auth()->user()->hasAnyRole(['admin', 'gerente']))
                <flux:table.column>
                    <span class="inline-flex items-center gap-1 text-azul_menu text-sm font-semibold">
                        <flux:icon name="wrench" class="text-azul_menu!" /> Acciones
                    </span>
                </flux:table.column>
                @endif

            </flux:table.columns>

            <flux:table.rows>
                @forelse ($this->rutas as $ruta)
                <flux:table.row :key="$ruta->id_ruta">

                    <flux:table.cell  align="center" class="whitespace-nowrap" title="{{ $ruta->nombre }}">
                        {{ $ruta->nombre }}
                    </flux:table.cell>

                    <flux:table.cell class="whitespace-nowrap col-hide-md">
                        {{ $ruta->sucursalSalida?->nombre ?? 'N/A' }}
                    </flux:table.cell>

                    <flux:table.cell class="whitespace-nowrap col-hide-md">
                        {{ $ruta->sucursalLlegada?->nombre ?? 'N/A' }}
                    </flux:table.cell>

                    <flux:table.cell  align="center" class="whitespace-nowrap col-hide-md">
                        <flux:badge color="zinc"> {{ $ruta->distancia }} km</flux:badge> 
                    </flux:table.cell>

                    <flux:table.cell  align="center" class="whitespace-nowrap">
                       <flux:badge color="zinc">{{ $ruta->tiempo_estimado }}</flux:badge> 
                    </flux:table.cell>

                    <flux:table.cell  align="center" class="whitespace-nowrap col-hide-md">
                        <flux:badge color="green">${{ number_format($ruta->tarifa_clientes, 2) }}</flux:badge>
                    </flux:table.cell>

                    <flux:table.cell  align="center" class="whitespace-nowrap col-hide-md">
                        <flux:badge color="green">${{ number_format($ruta->tarifa_paquete, 2) }}</flux:badge>
                    </flux:table.cell>

                    @can('update', $ruta)
                    <flux:table.cell>
                        <div class="flex items-center gap-1">
                            <flux:button size="sm" variant="ghost" icon="pencil" class="!text-azul_menu"
                                wire:click="$dispatch('edicion-ruta', { id: {{ $ruta->id_ruta }} })">
                            </flux:button>
                            <flux:button size="sm" variant="ghost" icon="trash" class="!text-rojo_texto"
                                wire:click="$dispatch('eliminacion-ruta', { id: {{ $ruta->id_ruta }} })">
                            </flux:button>
                        </div>
                    </flux:table.cell>
                    @endcan

                </flux:table.row>
                @empty
                <flux:table.row>
                    <flux:table.cell>
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
<?php

use App\Models\Corrida;
use App\Models\Urban;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

new class extends Component
{
    use WithPagination;

    public $idRuta = null;

    public $sortBy = 'id_corrida';
    public $sortDirection = 'desc'; // Descendente por defecto para historiales suele ser mejor
    public $search = '';
    public $perPage = 7;
    public $filtroEstado = '';

    public function mount($idRuta)
    {
        $this->idRuta = $idRuta;
    }

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

    
    #[On('corrida-creada')]
    #[On('corrida-actualizada')]
    #[On('corrida-eliminada')]
    public function refreshAfterCreate()
    {
        $this->resetPage();
    }

    

    #[Computed]
    public function corridas()
    {
        return Corrida::query()
            ->where('id_ruta', $this->idRuta)
            ->where('estado','Programada')
            ->when($this->search !== '', function ($query) {
                $query->where(function ($q) {
                    $q->whereHas('ruta', function ($sq) {
                        $sq->whereRaw('LOWER(nombre) like ?', ['%'.strtolower($this->search).'%']);
                    })->orWhereHas('user', function ($sq) {
                        $sq->whereRaw('LOWER(name) like ?', ['%'.strtolower($this->search).'%']);
                    });
                });
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);
    }
};
?>

<div class="py-2">

    <livewire:barra-busqueda placeholder="Buscar por ruta o conductor" />

    <flux:card>
        <flux:table :paginate="$this->corridas" dense>
            <flux:table.columns>

                <flux:table.column>
                    <span class="inline-flex items-center gap-1 text-azul_menu text-sm font-semibold">
                        <flux:icon name="map-pin-house" class="text-azul_menu!" /> Ruta
                    </span>
                </flux:table.column>

                <flux:table.column>
                    <span class="inline-flex items-center gap-1 text-azul_menu text-sm font-semibold">
                        <flux:icon name="ticket" class="text-azul_menu!" /> Conductor
                    </span>
                </flux:table.column>

                <flux:table.column>
                    <span class="inline-flex items-center gap-1 text-azul_menu text-sm font-semibold">
                        <flux:icon name="waypoints" class="text-azul_menu!" /> Estado
                    </span>
                </flux:table.column>

                <flux:table.column
                    class="col-hide-md"
                    sortable
                    :sorted="$sortBy === 'datetime_salida'"
                    :direction="$sortDirection"
                    wire:click="sort('datetime_salida')"
                >
                    <span class="inline-flex items-center gap-1 text-azul_menu text-sm font-semibold">
                        <flux:icon name="calendar" class="text-azul_menu!" /> Fecha
                    </span>
                </flux:table.column>

                <flux:table.column>
                    <span class="inline-flex items-center gap-1 text-azul_menu text-sm font-semibold">
                        <flux:icon name="alarm-clock" class="text-azul_menu!" /> Salida
                    </span>
                </flux:table.column>

                <flux:table.column align="center">
                    <span class="inline-flex items-center gap-3 text-azul_menu text-sm font-semibold">
                        <flux:icon name="alarm-clock" class="text-azul_menu!" /> Llegada <br> aproximada
                    </span>
                </flux:table.column>

                @if(auth()->user()->hasAnyRole(['admin', 'gerente']))

                    <flux:table.column>
                        <span class="inline-flex items-center gap-1 text-azul_menu text-sm font-semibold">
                            <flux:icon name="wrench" class="text-azul_menu!" /> Acciones
                        </span>
                    </flux:table.column>

                @endif

            </flux:table.columns>
            <flux:table.rows>
                @forelse ($this->corridas as $corrida)
                <flux:table.row :key="$corrida->id_corrida">

                    <flux:table.cell>
                        <div class="truncate" title="{{ $corrida->ruta->nombre ?? 'Sin ruta' }}">
                            {{ $corrida->ruta->nombre ?? 'Sin ruta' }}
                        </div>
                    </flux:table.cell>

                    <flux:table.cell>
                        <div class="truncate" title="{{ $corrida->user->name ?? 'Sin conductor' }}">
                            {{ $corrida->user->name ?? 'Sin conductor' }}
                        </div>
                    </flux:table.cell>

                    <flux:table.cell class="col-hide-md">
                        @php
                            $badgeColor = match ($corrida->estado) {
                                'En viaje' => 'amber',
                                'Cancelada' => 'red',
                                'Programada' => 'blue',
                                'Finalizada' => 'green',
                                default => 'zinc',
                            };
                        @endphp
                        <flux:badge color="{{ $badgeColor }}">{{ $corrida->estado }}</flux:badge>
                    </flux:table.cell>

                    <flux:table.cell class="col-hide-md">
                        {{ $corrida->datetime_salida ? $corrida->datetime_salida->format('d/m/Y') : '-' }}
                    </flux:table.cell>

                    <flux:table.cell class="tabular-nums" variant="strong">
                        <flux:badge color="emerald">
                            @if($corrida->datetime_salida)
                                {{ $corrida->datetime_salida->format('h:i') }}
                                {{ $corrida->datetime_salida->format('H') < 12 ? 'AM' : 'PM' }}
                            @else
                                -
                            @endif
                        </flux:badge>
                    </flux:table.cell>

                    <flux:table.cell align="center" class="tabular-nums" variant="strong">
                        <flux:badge color="blue">
                            @if($corrida->datetime_llegada)
                                {{ $corrida->datetime_llegada->format('h:i') }}
                                {{ $corrida->datetime_llegada->format('H') < 12 ? 'AM' : 'PM' }}
                            @else
                                -
                            @endif
                        </flux:badge>
                    </flux:table.cell>

                    <flux:table.cell>
                        <div class="flex gap-4">

                            <flux:button size="sm" icon="git-compare-arrows" class="text-texto-fondo! bg-fondo-amarillo! hover:bg-hover-amarillo! hover:text-white! border-none! btn-animado"
                                wire:click="$dispatch('cambio-estado-corrida', { id: {{ $corrida->id_corrida }} })">
                            </flux:button>

                            <flux:button size="sm" variant="ghost" icon="pencil" class="bg-azul_rebajado! text-azul_menu! hover:bg-azul_menu! hover:text-white! border-none! btn-animado"
                                wire:click="$dispatch('edicion-corrida', { id: {{ $corrida->id_corrida }} })">
                            </flux:button>

                        </div>
                    </flux:table.cell>

                </flux:table.row>
                @empty
                <flux:table.row>
                    <flux:table.cell colspan="7">
                        No se encontraron corridas para esta urban.
                    </flux:table.cell>
                </flux:table.row>
                @endforelse
            </flux:table.rows>

        </flux:table>

        <flux:select size="sm" class="w-full sm:w-auto mt-4" wire:model.live="perPage">
            <flux:select.option value="7">7</flux:select.option>
            <flux:select.option value="14">14</flux:select.option>
            <flux:select.option value="27">27</flux:select.option>
            <flux:select.option value="48">48</flux:select.option>
        </flux:select>
    </flux:card>

</div>

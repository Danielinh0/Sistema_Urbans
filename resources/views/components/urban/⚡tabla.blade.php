<?php

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use App\Models\Urban;

new class extends Component {
    use WithPagination;

    public $sortBy = 'id_urban';
    public $sortDirection = 'asc';
    public $search = '';
    public $perPage = 10;
    public $filtroAsientos = '';
    public $filtroEstado = '';

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

    #[On('urban-creada')]
    #[On('urban-eliminada')]
    #[On('urban-actualizada')]
    public function refreshAfterCreate()
    {
        $this->resetPage();
    }

    #[On('filterUpdated')]
    public function aplicarFiltro($filters)
    {
        $this->filtroEstado = $filters['estado'] ?? '';
        $this->filtroAsientos = $filters['numero_asientos'] ?? '';
        $this->resetPage();
    }

    #[Computed]
    public function urbans()
    {
        return Urban::query()
            ->with('socio')
            ->when($this->search !== '', function ($query) {
                $query->whereRaw('LOWER(codigo_urban) like ?', ['%' . strtolower($this->search) . '%']);
            })
            ->when($this->filtroEstado === 'Activa', function ($query) {
                $query->where('estado', 'Activa');
            })
            ->when($this->filtroEstado === 'Inactiva', function ($query) {
                $query->where('estado', 'Inactiva');
            })
            ->when($this->filtroEstado === 'Fuera de servicio', function ($query) {
                $query->where('estado', 'Fuera de servicio');
            })
            ->when($this->filtroEstado === 'Mantenimiento', function ($query) {
                $query->where('estado', 'Mantenimiento');
            })
            ->when($this->filtroAsientos === '10', function ($query) {
                $query->where('numero_asientos', 10);
            })
            ->when($this->filtroAsientos === '15', function ($query) {
                $query->where('numero_asientos', 15);
            })
            ->when($this->filtroAsientos === '20', function ($query) {
                $query->where('numero_asientos', 20);
            })
            ->withTrashed()
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);
    }
};
?>

<div>
    <livewire:barra-busqueda placeholder="Busca una urban por su código..." :filters="[
        'numero_asientos' => [
            'label' => 'Asientos',
            'options' => [
                '10' => '10 asientos',
                '15' => '15 asientos',
                '20' => '20 asientos'
            ]
        ],
        'estado' => [
            'label' => 'Estado',
            'options' => ['Activa', 'Fuera de servicio', 'Mantenimiento', 'Inactiva']
        ]
    ]" />
    <flux:card class="px-12 py-10" >
        <flux:table :paginate="$this->urbans">
            <flux:table.columns>
                

                <x-header-table align="center" icon="arrow-up-0-1" sortable="codigo_urban" :sortBy="$sortBy" :sortDirection="$sortDirection">Código</x-header-table>

                <x-header-table icon="armchair" sortable="numero_asientos" align="center"
                    :sortBy="$sortBy" :sortDirection="$sortDirection">Asientos</x-header-table>

                <x-header-table icon="bus" sortable="placa" :sortBy="$sortBy" :sortDirection="$sortDirection">Placa</x-header-table>
                <x-header-table  icon="activity" sortable="estado" :sortBy="$sortBy" :sortDirection="$sortDirection">Estado</x-header-table>
                <x-header-table icon="user" sortable="id_socio" :sortBy="$sortBy" :sortDirection="$sortDirection">Socio</x-header-table>

                <x-header-table icon="wrench" align="center">Acciones</x-header-table>
            </flux:table.columns>

            <flux:table.rows>
                @forelse ($this->urbans as $urban)
                    <flux:table.row :key="$urban->id_urban">
                        
                        <flux:table.cell align="center">
                            <flux:badge color="cyan">{{ $urban->codigo_urban }}</flux:badge>
                        </flux:table.cell>


                        <flux:table.cell align="center">
                            {{ $urban->numero_asientos }}
                        </flux:table.cell>

                        <flux:table.cell>{{ $urban->placa }}</flux:table.cell>

                        <flux:table.cell>
                            @if ($urban->estado == 'Activa')
                                <flux:badge color="green">Activa</flux:badge>
                            @elseif ($urban->estado == 'Inactiva')
                                <flux:badge color="purple">Inactiva</flux:badge>
                            @elseif ($urban->estado == 'Fuera de servicio')
                                <flux:badge color="sky">Fuera de servicio</flux:badge>
                            @elseif ($urban->estado == 'Mantenimiento')
                                <flux:badge color="yellow">Mantenimiento</flux:badge>
                            @endif
                        </flux:table.cell>


                        <flux:table.cell >
                            {{ $urban->socio->nombre . ' ' . $urban->socio->apellido_paterno }}
                        </flux:table.cell>

                        <flux:table.cell align="center" class="flex justify-center gap-4">
                            @can('update', $urban)
                                @if ($urban->estado !== 'Inactiva')

                                 <flux:button href="{{ route('urban.show', $urban->id_urban) }}" icon="eye" class="text-texto-fondo! bg-fondo-amarillo! hover:bg-hover-amarillo! hover:text-white! border-none! btn-animado">
                                        
                                </flux:button>

                                <flux:button size="sm" icon="pencil" class="bg-azul_rebajado! text-azul_menu! hover:bg-azul_menu! hover:text-white! border-none! btn-animado"
                                    wire:click="$dispatch('preparar-edicion-urban', { id: {{ $urban->id_urban }} })">
                                    
                                </flux:button>
                                @endif
                            @endcan

                            @can('delete', $urban)
                            @if ($urban->estado !== 'Inactiva')
                            <flux:button size="sm" variant="ghost" icon="chevron-double-down" class="bg-fondo-rojo! text-texto-rojo! hover:bg-texto-rojo! hover:text-white! border-none! btn-animado"

                                wire:click="$dispatch('preparar-eliminacion-urban', { id: {{ $urban->id_urban }} })">
                                </flux:button>
                            @else
                                <flux:button size="sm" icon="chevron-double-up" class="bg-verde-hover! text-verde-confirmacion! hover:bg-verde-confirmacion! hover:text-verde-hover! border-none! btn-animado"
                                    wire:click="$dispatch('preparar-activacion-urban', { id: {{ $urban->id_urban }} })">
                                    Activar
                                </flux:button>
                            
                                @endif
                            
                                @endcan
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="6" class="text-center py-4">
                            No se encontraron urbans.
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
        <flux:select size="sm" class="w-full sm:w-auto" wire:model.live="perPage">
            <flux:select.option value="10">10</flux:select.option>
            <flux:select.option value="25">25</flux:select.option>
            <flux:select.option value="50">50</flux:select.option>
        </flux:select>
    </flux:card>

    <livewire:urban.manager />
</div>
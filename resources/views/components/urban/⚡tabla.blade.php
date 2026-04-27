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
    public $perPage = 6;
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
        $this->filtroEstado = $filters['estado_viaje'] ?? '';
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
            ->when($this->filtroEstado === 'ocupado', function ($query) {
                $query->conViajesPendientes();
            })
            ->when($this->filtroEstado === 'libre', function ($query) {
                $query->whereDoesntHave('corrida', function ($q) {
                    $q->where('hora_llegada', '>=', now());
                });
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
        'estado_viaje' => [
            'label' => 'Disponibilidad',
            'options' => ['ocupado' => 'En viaje / Pendiente', 'libre' => 'Sin viajes próximos']
        ]
    ]" />
    <flux:card>
        <flux:table :paginate="$this->urbans" horizontal class="w-full">
            <flux:table.columns>
                <x-header-table sortable="id_urban" :sortBy="$sortBy" :sortDirection="$sortDirection">ID</x-header-table>

                <x-header-table sortable="codigo_urban" :sortBy="$sortBy" :sortDirection="$sortDirection">Código</x-header-table>

                <x-header-table icon="armchair" sortable="numero_asientos"
                    :sortBy="$sortBy" :sortDirection="$sortDirection">Asientos</x-header-table>

                <x-header-table icon="bus" sortable="placa" :sortBy="$sortBy" :sortDirection="$sortDirection">Placa</x-header-table>

                <x-header-table icon="user" sortable="id_socio" :sortBy="$sortBy" :sortDirection="$sortDirection">Socio</x-header-table>

                <x-header-table align="center">Acciones</x-header-table>
            </flux:table.columns>

            <flux:table.rows>
                @forelse ($this->urbans as $urban)
                    <flux:table.row :key="$urban->id_urban">
                        <flux:table.cell>{{ $urban->id_urban }}</flux:table.cell>
                        <flux:table.cell variant="strong">
                            <flux:badge color="orange">{{ $urban->codigo_urban }}</flux:badge>
                        </flux:table.cell>
                        <flux:table.cell class="pl-15">{{ $urban->numero_asientos }}</flux:table.cell>
                        <flux:table.cell>{{ $urban->placa }}</flux:table.cell>
                        <flux:table.cell>
                            {{ $urban->socio->nombre . ' ' . $urban->socio->apellido_paterno }}
                        </flux:table.cell>

                        <flux:table.cell class="flex gap-3 justify-end">
                            {{-- Botón Editar: Solo gerente --}}
                            @can('update', $urban)
                            <x-boton-estilo
                                bg="bg-azul_menu"
                                c_text="text-white"
                                icon="pencil"
                                text="Editar"
                                evento="$dispatch('preparar-edicion-urban', { id: {{ $urban->id_urban }} })" />
                            @endcan

                            {{-- Botón Eliminar: Solo gerente --}}
                            @can('delete', $urban)
                            <x-boton-estilo
                                bg="bg-rojo_boton"
                                c_text="text-rojo_texto"
                                icon="trash"
                                text="Eliminar"
                                evento="$dispatch('preparar-eliminacion-urban', { id: {{ $urban->id_urban }} })" />
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
            <flux:select.option value="6">6</flux:select.option>
            <flux:select.option value="12">12</flux:select.option>
            <flux:select.option value="24">24</flux:select.option>
        </flux:select>
    </flux:card>

    <livewire:urban.manager />
</div>
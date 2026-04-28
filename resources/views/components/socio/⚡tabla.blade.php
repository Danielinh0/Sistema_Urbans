<?php

use Livewire\WithPagination;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use App\Models\Socio;

new class extends Component {
    use WithPagination;

    public $sortBy = 'id_socio';
    public $sortDirection = 'asc';
    public $search = '';
    public $perPage = 6;
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

    #[On('socio-creado')]
    #[On('socio-eliminado')]
    #[On('socio-actualizado')]
    public function refreshAfterCreate()
    {
        $this->resetPage();
    }

    #[On('filterUpdated')]
    public function aplicarFiltros($filters)
    {
        $this->filtroEstado = $filters['estado'] ?? '';
        $this->resetPage();
    }

    #[Computed]
    public function socios()
    {
        return Socio::query()
            ->with('urbans')
            ->when($this->search !== '', function ($query) {
                $query->whereRaw('LOWER(nombre) like ?', ['%' . strtolower($this->search) . '%']);
            })
            ->when($this->filtroEstado !== '', function ($query) {
                $query->where('estado', $this->filtroEstado);
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);
    }
};
?>

<div>
    <livewire:barra-busqueda placeholder="Busca un socio por su nombre..." :filters="[
        'estado' => [
            'label' => 'Estado',
            'options' => ['Activo' => 'Activo', 'Inactivo' => 'Inactivo']
        ]
    ]" />

    <flux:card>
        <flux:table :paginate="$this->socios">
            <flux:table.columns>
                <x-header-table sortable :sorted="$sortBy === 'id_socio'" :direction="$sortDirection"
                    wire:click="sort('id_socio')">ID</x-header-table>
                <x-header-table sortable :sorted="$sortBy === 'nombre'" :direction="$sortDirection"
                    wire:click="sort('nombre')">Nombre</x-header-table>
                <x-header-table sortable :sorted="$sortBy === 'apellido_paterno'" :direction="$sortDirection"
                    wire:click="sort('apellido_paterno')">Apellido Paterno</x-header-table>
                <x-header-table sortable :sorted="$sortBy === 'apellido_materno'" :direction="$sortDirection"
                    wire:click="sort('apellido_materno')">Apellido Materno</x-header-table>
                <x-header-table icon="activity" sortable :sorted="$sortBy === 'estado'" :direction="$sortDirection"
                    wire:click="sort('estado')">Estado</x-header-table>
                <x-header-table icon="calendar" sortable :sorted="$sortBy === 'fecha_de_incorporacion'"
                    :direction="$sortDirection"
                    wire:click="sort('fecha_de_incorporacion')">Incorporación</x-header-table>
                <x-header-table icon="smartphone" sortable :sorted="$sortBy === 'numero_telefonico'"
                    :direction="$sortDirection" wire:click="sort('numero_telefonico')">Teléfono</x-header-table>
                <x-header-table icon="mail" sortable :sorted="$sortBy === 'correo'" :direction="$sortDirection"
                    wire:click="sort('correo')">Correo</x-header-table>
                <x-header-table icon="bus">Urbans</x-header-table>
                <x-header-table icon="layout-grid" align="center">Acciones</x-header-table>
            </flux:table.columns>
            <flux:table.rows>
                @forelse ($this->socios as $socio)
                    <flux:table.row :key="$socio->id_socio">
                        <flux:table.cell>
                            {{ $socio->id_socio }}
                        </flux:table.cell>
                        <flux:table.cell>
                            {{ $socio->nombre }}
                        </flux:table.cell>
                        <flux:table.cell>
                            {{ $socio->apellido_paterno }}
                        </flux:table.cell>
                        <flux:table.cell>
                            {{ $socio->apellido_materno }}
                        </flux:table.cell>
                        <flux:table.cell class="text-center!">
                            @if ($socio->estado == 'Activo')
                                <flux:badge color="green">Activo</flux:badge>
                            @else
                                <flux:badge color="red">Inactivo</flux:badge>
                            @endif
                        </flux:table.cell>
                        <flux:table.cell class="text-center!">
                            {{ $socio->fecha_de_incorporacion }}
                        </flux:table.cell>
                        <flux:table.cell>
                            {{ $socio->numero_telefonico }}
                        </flux:table.cell>
                        <flux:table.cell>
                            {{ $socio->correo }}
                        </flux:table.cell>
                        <flux:table.cell>
                            @foreach ($socio->urbans as $urban)
                                <flux:badge color="cyan">{{ $urban->codigo_urban }}</flux:badge>
                            @endforeach
                        </flux:table.cell>
                        <flux:table.cell class="flex gap-2">
                            <flux:button variant="ghost" icon="pencil" class="!text-azul_menu"
                                wire:click="$dispatch('preparar-edicion-socio', { id: {{ $socio->id_socio }} })">
                                <span class="hidden xl:inline ml-1">Editar</span>
                            </flux:button>
                            <flux:button variant="ghost" icon="trash" class="!text-rojo_texto"
                                wire:click="$dispatch('preparar-eliminacion-socio', { id: {{ $socio->id_socio }} })">
                                <span class="hidden xl:inline ml-1">Eliminar</span>
                            </flux:button>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="8" class="text-center py-4 ">
                            No se encontraron socios.
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

    <!-- Botones de editar y eliminar -->
    <livewire:socio.manager />
</div>
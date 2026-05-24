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
        <div class="overflow-x-auto">
            <flux:table :paginate="$this->socios">
            <flux:table.columns>
                
                <x-header-table sortable :sorted="$sortBy === 'nombre'" :direction="$sortDirection"
                    wire:click="sort('nombre')" class="min-w-[100px]">Nombre</x-header-table>
                <x-header-table sortable :sorted="$sortBy === 'apellido_paterno'" :direction="$sortDirection"
                    wire:click="sort('apellido_paterno')" class="min-w-[100px]">A. Paterno</x-header-table>
                <x-header-table sortable :sorted="$sortBy === 'apellido_materno'" :direction="$sortDirection"
                    wire:click="sort('apellido_materno')" class="min-w-[100px]">A. Materno</x-header-table>
                <x-header-table icon="activity" sortable :sorted="$sortBy === 'estado'" :direction="$sortDirection"
                    wire:click="sort('estado')" class="w-16 text-center!">Estado</x-header-table>
                <x-header-table icon="calendar" sortable :sorted="$sortBy === 'fecha_de_incorporacion'"
                    :direction="$sortDirection"
                    wire:click="sort('fecha_de_incorporacion')" class="w-20 text-center!">Incorp.</x-header-table>
                <x-header-table icon="smartphone" sortable :sorted="$sortBy === 'numero_telefonico'"
                    :direction="$sortDirection" wire:click="sort('numero_telefonico')" class="w-28">Contacto</x-header-table>
                <x-header-table icon="bus" class="w-16 text-center!">Urbans</x-header-table>
                @if(auth()->user()->hasAnyRole(['gerente', 'admin']))
                    <x-header-table align="center" class="w-20">Acciones</x-header-table>
                @endif
            </flux:table.columns>
            <flux:table.rows>
                @forelse ($this->socios as $socio)
                    <flux:table.row :key="$socio->id_socio">
                        
                        <flux:table.cell class="font-medium p-1">
                            <span class="text-sm truncate">{{ $socio->nombre }}</span>
                        </flux:table.cell>
                        <flux:table.cell class="font-medium p-1">
                            <span class="text-sm truncate">{{ $socio->apellido_paterno }}</span>
                        </flux:table.cell>
                        <flux:table.cell class="font-medium p-1">
                            <span class="text-sm truncate">{{ $socio->apellido_materno }}</span>
                        </flux:table.cell>
                        <flux:table.cell class="text-center! p-1">
                            @if ($socio->estado == 'Activo')
                                <flux:badge color="green" class="text-xs! px-1">Activo</flux:badge>
                            @else
                                <flux:badge color="red" class="text-xs! px-1">Inactivo</flux:badge>
                            @endif
                        </flux:table.cell>
                        <flux:table.cell class="text-center! p-1">
                            <span class="text-xs">{{ \Carbon\Carbon::parse($socio->fecha_de_incorporacion)->format('d/m/Y') }}</span>
                        </flux:table.cell>
                        <flux:table.cell class="p-1">
                            <div class="flex flex-col gap-0.5">
                                @if($socio->numero_telefonico)
                                    <div class="flex items-center gap-1 text-xs">
                                        <flux:icon name="phone" class="w-3 h-3" />
                                        <span class="truncate">{{ $socio->numero_telefonico }}</span>
                                    </div>
                                @endif
                                @if($socio->correo)
                                    <div class="flex items-center gap-1 text-xs">
                                        <flux:icon name="mail" class="w-3 h-3" />
                                        <span class="truncate">{{ $socio->correo }}</span>
                                    </div>
                                @endif
                            </div>
                        </flux:table.cell>

                        <flux:table.cell class="text-center! p-1">
                            @if($socio->urbans->count() > 0)
                                <flux:dropdown>
                                    <flux:button variant="ghost" icon="bus" size="sm" class="p-1">
                                        <span class="text-xs">{{ $socio->urbans->count() }}</span>
                                    </flux:button>
                                    <flux:menu>
                                        @foreach ($socio->urbans as $urban)
                                            <flux:menu.item icon="bus" class="text-xs">{{ $urban->codigo_urban }}</flux:menu.item>
                                        @endforeach
                                    </flux:menu>
                                </flux:dropdown>
                            @else
                                <span class="text-gray-400 text-xs">-</span>
                            @endif
                        </flux:table.cell>
                        <flux:table.cell class="flex gap-1 justify-center p-1">
                            @can('update', $socio)
                                <flux:button variant="ghost" icon="user-round-pen" class="!text-azul_menu p-1"
                                    wire:click="$dispatch('preparar-edicion-socio', { id: {{ $socio->id_socio }} })"
                                    title="Editar">
                                </flux:button>
                            @endcan
                            @can('delete', $socio)
                                <flux:button variant="ghost" icon="user-round-minus" class="!text-rojo_texto p-1"
                                    wire:click="$dispatch('preparar-eliminacion-socio', { id: {{ $socio->id_socio }} })"
                                    title="Eliminar">
                                </flux:button>
                            @endcan
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
        </div>
        <flux:select size="sm" class="w-full sm:w-auto mt-4" wire:model.live="perPage">
            <flux:select.option value="6">6</flux:select.option>
            <flux:select.option value="12">12</flux:select.option>
            <flux:select.option value="24">24</flux:select.option>
        </flux:select>
    </flux:card>

    <!-- Botones de editar y eliminar -->
    <livewire:socio.manager />
</div>
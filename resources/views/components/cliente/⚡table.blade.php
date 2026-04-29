<?php

use Livewire\WithPagination;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use App\Models\Cliente;

new class extends Component
{
    use WithPagination;

    public $sortBy = 'id_cliente';
    public $sortDirection = 'desc';
    public $search = '';

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

    #[On('cliente-creado')]
    #[On('cliente-eliminado')]
    #[On('cliente-actualizado')]
    public function refreshAfterCreate()
    {
        $this->resetPage();
    }

    #[Computed]
    public function clientes()
    {
        $search = trim($this->search);
        return Cliente::query()
            ->when($this->search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('nombre', 'ILIKE', "%{$search}%")
                        ->orWhere('apellido_paterno', 'ILIKE', "%{$search}%")
                        ->orWhere('apellido_materno', 'ILIKE', "%{$search}%");
                });
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate(10);
    }
};
?>

<div>
    <livewire:barra-busqueda placeholder="Buscar un Cliente..." />
    <div>
        <flux:card>
            <flux:table :paginate="$this->clientes">
                <flux:table.columns>
                    <x-header-table sortable="id_cliente" :sortBy="$sortBy" :sortDirection="$sortDirection">ID</x-header-table>
                    <x-header-table icon="user-round" sortable="nombre" :sortBy="$sortBy" :sortDirection="$sortDirection">Nombre</x-header-table>
                    <x-header-table sortable="apellido_paterno" :sortBy="$sortBy" :sortDirection="$sortDirection">Apellido Paterno</x-header-table>
                    <x-header-table sortable="apellido_materno" :sortBy="$sortBy" :sortDirection="$sortDirection">Apellido Materno</x-header-table>
                    <x-header-table icon="shopping-bag">Cantidad de compras</x-header-table>
                    @if(auth()->user()->hasAnyRole(['administrador', 'gerente']))
                    <x-header-table icon="layout-grid" align="center">Acciones</x-header-table>
                    @endif
                </flux:table.columns>
                <flux:table.rows>
                    @forelse ($this->clientes as $cliente)
                    <flux:table.row :key="$cliente->id_cliente">
                        <flux:table.cell>{{ $cliente->id_cliente }}</flux:table.cell>
                        <flux:table.cell>{{ $cliente->nombre }}</flux:table.cell>
                        <flux:table.cell>{{ $cliente->apellido_paterno }}</flux:table.cell>
                        <flux:table.cell>{{ $cliente->apellido_materno }}</flux:table.cell>
                        <flux:table.cell>
                            <flux:badge color="cyan">{{ $cliente->ventas->count() }}</flux:badge>
                        </flux:table.cell>

                        @can('update', $cliente)
                        <flux:table.cell class="flex gap-2">
                            <flux:button variant="ghost" icon="pencil" class="!text-azul_menu"
                                wire:click="$dispatch('preparar-edicion-cliente', { id: {{ $cliente->id_cliente }} })">
                                <span class="hidden xl:inline ml-1">Editar</span>
                            </flux:button>
                            @if (!$cliente->ventas->count()>0 and !$cliente->boletos->count()>0)
                            <flux:button variant="ghost" icon="trash" class="!text-rojo_texto"
                                wire:click="$dispatch('preparar-eliminacion-cliente', { id: {{ $cliente->id_cliente }} })">
                                <span class="hidden xl:inline ml-1">Eliminar</span>
                            </flux:button>
                            @endif
                        </flux:table.cell>
                        @endcan
                    </flux:table.row>
                    @empty
                    <flux:table.row>
                        <flux:table.cell colspan="6" class="text-center py-4">
                            No se encontraron clientes.
                        </flux:table.cell>
                    </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        </flux:card>
        <livewire:cliente.manager />
    </div>
</div>
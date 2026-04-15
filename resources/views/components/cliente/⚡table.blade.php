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

    #[On('cliente-creado')]
    #[On('cliente-eliminado')]
    #[On('cliente-actualizado')]
    public function refreshAfterCreate()
    {
        $this->resetPage();
    }

    #[Computed]
    public function clientes(){
        return Cliente::query()
            ->when($this->search !== '', function ($query) {
                $query->whereRaw('LOWER(nombre) like ?', ['%'.strtolower($this->search).'%']);
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate(10);
    }
};
?>

<div>
    <div>
        <flux:card>
            <flux:table :paginate="$this->clientes">
                <flux:table.columns >
                    <flux:table.column sortable :sorted="$sortBy === 'id_cliente'" :direction="$sortDirection" wire:click="sort('id_cliente')">ID</flux:table.column>
                    <flux:table.column sortable :sorted="$sortBy === 'nombre'" :direction="$sortDirection" wire:click="sort('nombre')">Nombre</flux:table.column>
                    <flux:table.column sortable :sorted="$sortBy === 'apellido_paterno'" :direction="$sortDirection" wire:click="sort('apellido_paterno')">Apellido Paterno</flux:table.column>
                    <flux:table.column sortable :sorted="$sortBy === 'apellido_materno'" :direction="$sortDirection" wire:click="sort('apellido_materno')">Apellido Materno</flux:table.column>
                    <flux:table.column>Cantidad de compras</flux:table.column>
                    <flux:table.column>Acciones</flux:table.column>
                </flux:table.columns>
                <flux:table.rows>
                    @forelse ($this->clientes as $cliente)
                        <flux:table.row :key="$cliente->id_cliente">
                            <flux:table.cell>
                                {{ $cliente->id_cliente }}
                            </flux:table.cell>
                            <flux:table.cell>
                                {{ $cliente->nombre }}
                            </flux:table.cell>
                            <flux:table.cell>
                                {{ $cliente->apellido_paterno }}
                            </flux:table.cell>
                            <flux:table.cell>
                                {{ $cliente->apellido_materno }}
                            </flux:table.cell>
                            <flux:table.cell>
                                {{ $cliente->ventas->count() }}
                            </flux:table.cell>
                            <flux:table.cell class="flex gap-2">
                                <flux:button variant="ghost" icon="pencil" class="!text-azul_menu"
                                    wire:click="$dispatch('preparar-edicion-cliente', { id: {{ $cliente->id_cliente }} })">
                                    Editar
                                </flux:button>
                                @if (!$cliente->ventas->count()>0 and !$cliente->boletos->count()>0)
                                    <flux:button variant="ghost" icon="trash" class="!text-rojo_texto"
                                        wire:click="$dispatch('preparar-eliminacion-cliente', { id: {{ $cliente->id_cliente }} })">
                                        Eliminar
                                    </flux:button>       
                                @endif
                                
                            </flux:table.cell>
                        </flux:table.row>
                        
                    @empty
                        
                    @endforelse
                </flux:table.rows>
            </flux:table>
        </flux:card>
        <livewire:cliente.manager />
    </div>
</div>
<?php

use Livewire\WithPagination;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use App\Models\User;

new class extends Component
{
    use WithPagination;

    public $sortBy = 'id_usuario';
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

    #[On('usuario-creado')]
    #[On('usuario-eliminado')]
    #[On('usuario-actualizado')]
    public function refreshAfterCreate()
    {
        $this->resetPage();
    }

    #[Computed]
    public function usuarios(){
        $allowedSorts = ['id_usuario', 'name', 'email', 'id_sucursal', 'rol_nombre'];
        $sortColumn = in_array($this->sortBy, $allowedSorts, true) ? $this->sortBy : 'id_usuario';
        return User::query()
            ->with(['sucursal', 'direccion.calle.colonia.codigoPostal.estado.pais'])
            ->withMin('roles as rol_nombre', 'name')
            ->when($this->search !== '', function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })
            ->orderBy($sortColumn, $this->sortDirection)
            ->paginate(10);
    }
};
?>

<div>
    <div>
        <flux:card>
            <flux:table :paginate="$this->usuarios">
                <flux:table.columns>
                    <flux:table.column sortable :sorted="$sortBy === 'id_usuario'" :direction="$sortDirection" wire:click="sort('id_usuario')">ID</flux:table.column>
                    <flux:table.column sortable :sorted="$sortBy === 'name'" :direction="$sortDirection" wire:click="sort('name')">Nombre</flux:table.column>
                    <flux:table.column sortable :sorted="$sortBy === 'email'" :direction="$sortDirection" wire:click="sort('email')">Email</flux:table.column>
                    <flux:table.column sortable :sorted="$sortBy === 'id_sucursal'" :direction="$sortDirection" wire:click="sort('id_sucursal')">Sucursal</flux:table.column>
                    <flux:table.column sortable :sorted="$sortBy === 'rol_nombre'" :direction="$sortDirection" wire:click="sort('rol_nombre')" >Tipo de usuario</flux:table.column>
                    <flux:table.column >Direccion</flux:table.column>
                    <flux:table.column>Acciones</flux:table.column>
                </flux:table.columns>
                <flux:table.rows>
                    @foreach ($this->usuarios as $usuario)
                        <flux:table.row :key="$usuario->id_usuario"> 
                            <flux:table.cell>{{ $usuario->id_usuario }}</flux:table.cell>
                            <flux:table.cell>{{ $usuario->name }}</flux:table.cell>
                            <flux:table.cell>{{ $usuario->email }}</flux:table.cell>
                            <flux:table.cell>{{ $usuario->sucursal ? $usuario->sucursal->nombre : 'N/A' }}</flux:table.cell>
                            <flux:table.cell>{{ $usuario->rol_nombre ?? 'N/A' }}</flux:table.cell>
                            <flux:table.cell>
                            {{$usuario->direccion->calle->nombre ?? ''}}
                            #{{$usuario->direccion->numero_exterior ?? ''}}, 
                            {{$usuario->direccion->calle->colonia->nombre ?? ''}}, 
                            {{$usuario->direccion->calle->colonia->codigoPostal->numero ?? ''}}, 
                            {{$usuario->direccion->calle->colonia->codigoPostal->estado->nombre ?? ''}},
                            {{$usuario->direccion->calle->colonia->codigoPostal->estado->pais->nombre ?? ''}}
                        </flux:table.cell>
                            <flux:table.cell>
                                <div class="flex gap-2">
                                    <flux:button icon="pencil" color="blue" wire:click="$emit('editarUsuario', {{ $usuario->id_usuario }})">Editar</flux:button>
                                    <flux:button icon="trash" color="red" wire:click="$emit('eliminarUsuario', {{ $usuario->id_usuario }})">Eliminar</flux:button>
                                </div>
                            </flux:table.cell>

                        </flux:table.row>
                        
                    @endforeach

                </flux:table.rows>
            </flux:table>
        </flux:card>
    </div>
</div>
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
        $search = trim($this->search);
        return User::query()
            ->with(['sucursal', 'direccion.calle.colonia.codigoPostal.estado.pais'])
            ->withMin('roles as rol_nombre', 'name')
            ->when($this->search !== '', function ($query) use ($search){
            $query->where(function ($q) use ($search) {
                $q->where('name', 'ILIKE', "%{$search}%")
                ->orWhere('apellido_paterno', 'ILIKE', "%{$search}%")
                ->orWhere('apellido_materno', 'ILIKE', "%{$search}%")
                ->orWhere('email', 'ILIKE', "%{$search}%")
                ->orWhereHas('sucursal', function($sq) use ($search){
                    $sq->where('nombre', 'ILIKE', "%{$search}%");
                })
                ->orWhereHas('roles', function($rq) use ($search){
                    $rq->where('name', 'ILIKE', "%{$search}%");
                });
            });
        })
            ->orderBy($sortColumn, $this->sortDirection)
            ->paginate(10);
    }
};
?>

<div>
    <livewire:barra-busqueda placeholder="Busca un Usuario..." />
    <div>
        <flux:card>
            <flux:table :paginate="$this->usuarios">
                <flux:table.columns>
                    <x-header-table sortable="id_usuario" :sortBy="$sortBy" :sortDirection="$sortDirection">ID</x-header-table>
                    <x-header-table icon="user-round" sortable="name" :sortBy="$sortBy" :sortDirection="$sortDirection">Nombre</x-header-table>
                    <x-header-table icon="mail" sortable="email" :sortBy="$sortBy" :sortDirection="$sortDirection">Email</x-header-table>
                    <x-header-table icon="building-2" sortable="id_sucursal" :sortBy="$sortBy" :sortDirection="$sortDirection">Sucursal</x-header-table>
                    <x-header-table icon="user-round-plus" sortable="rol_nombre" :sortBy="$sortBy" :sortDirection="$sortDirection">Tipo de usuario</x-header-table>
                    <x-header-table icon="map-pin-house">Direccion</x-header-table>
                    <x-header-table icon="layout-grid" align="center">Acciones</x-header-table>
                </flux:table.columns>
                <flux:table.rows>
                    @forelse ($this->usuarios as $usuario)
                        <flux:table.row :key="$usuario->id_usuario"> 
                            <flux:table.cell>{{ $usuario->id_usuario }}</flux:table.cell>
                            <flux:table.cell>{{ $usuario->name }} {{ $usuario->apellido_paterno }} {{ $usuario->apellido_materno }}</flux:table.cell>
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
                            <flux:table.cell class="flex gap-2">
                                <flux:button variant="ghost" icon="pencil" class="!text-azul_menu"
                                    wire:click="$dispatch('preparar-edicion-usuario', { id: {{ $usuario->id_usuario }} })">
                                    <span class="hidden xl:inline ml-1">Editar</span>
                                </flux:button>
                                @if (!$usuario->corridas->count()>0 || !$usuario->turnos->count()>0)
                                    <flux:button variant="ghost" icon="trash" class="!text-rojo_texto"
                                        wire:click="$dispatch('preparar-eliminacion-usuario', { id: {{ $usuario->id_usuario }} })">
                                        <span class="hidden xl:inline ml-1">Eliminar</span>
                                    </flux:button>
                                @endif
                                
                            </flux:table.cell>

                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="7" class="text-center py-4 ">
                                No se encontraron usuarios.
                            </flux:table.cell>
                        </flux:table.row>    
                    @endforelse

                </flux:table.rows>
            </flux:table>
        </flux:card>
        <livewire:usuario.manager />
    </div>
</div>
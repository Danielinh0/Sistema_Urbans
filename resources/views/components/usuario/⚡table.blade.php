<?php

use Livewire\WithPagination;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use App\Models\User;
use App\Models\Sucursal;

new class extends Component
{
    use WithPagination;

    public $sortBy = 'id_usuario';
    public $sortDirection = 'desc';
    public $search = '';
    public $filtroSucursal = '';
    public $filtroRol = '';

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

    #[On('usuario-creado')]
    #[On('usuario-eliminado')]
    #[On('usuario-actualizado')]
    public function refreshAfterCreate()
    {
        $this->resetPage();
    }

    #[On('filterUpdated')]
    public function aplicarFiltros($filters)
    {
        $this->filtroSucursal = $filters['id_sucursal'] ?? '';
        $this->filtroRol = $filters['rol_nombre'] ?? '';
        $this->resetPage();
    }

    #[Computed]
    public function sucursales(){
        return Sucursal::orderBy('nombre')->get();
    }

    #[Computed]
    public function all_roles(){
        return \Spatie\Permission\Models\Role::orderBy('name')->get();
    }

    #[Computed]
    public function usuarios()
    {
        $allowedSorts = ['id_usuario', 'name', 'email', 'id_sucursal', 'rol_nombre'];
        $sortColumn = in_array($this->sortBy, $allowedSorts, true) ? $this->sortBy : 'id_usuario';
        $search = trim($this->search);

        return User::query()
            ->with(['sucursal', 'direccion.calle.colonia.codigoPostal.estado.pais'])
            ->withCount(['corridas', 'turnos'])
            ->withMin('roles as rol_nombre', 'name')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'ILIKE', "%{$search}%")
                        ->orWhere('apellido_paterno', 'ILIKE', "%{$search}%")
                        ->orWhere('apellido_materno', 'ILIKE', "%{$search}%")
                        ->orWhere('email', 'ILIKE', "%{$search}%")
                        ->orWhereHas('sucursal', function ($sq) use ($search) {
                            $sq->where('nombre', 'ILIKE', "%{$search}%");
                        })
                        ->orWhereHas('roles', function ($rq) use ($search) {
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
        <flux:card class="!p-2 overflow-x-auto">
            <flux:table :paginate="$this->usuarios" class="w-full min-w-[34rem] md:min-w-[46rem] xl:min-w-[70rem] text-sm compact-table usuarios-table" dense>
                <flux:table.columns>
                    <x-header-table sortable="id_usuario" class="w-[3.25rem] text-center col-hide-sm" :sortBy="$sortBy" :sortDirection="$sortDirection">
                        ID
                    </x-header-table>

                    <x-header-table sortable="name" class="w-[11rem]" :sortBy="$sortBy" :sortDirection="$sortDirection">
                        Nombre
                    </x-header-table>

                    <x-header-table icon="mail" sortable="email" class="w-[13rem] col-hide-md" :sortBy="$sortBy" :sortDirection="$sortDirection">
                        Email
                    </x-header-table>

                    <x-header-table icon="building-2" sortable="id_sucursal" class="w-[13rem]" :sortBy="$sortBy" :sortDirection="$sortDirection">
                        Sucursal
                    </x-header-table>

                    <x-header-table icon="shield-check" sortable="rol_nombre" class="w-[6rem] text-center" :sortBy="$sortBy" :sortDirection="$sortDirection">
                        Tipo
                    </x-header-table>

                    <x-header-table icon="map-pin" class="w-[15rem] col-hide-lg">
                        Dirección
                    </x-header-table>

                    <x-header-table icon="wrench" class="w-[10rem] text-center">
                        Acciones
                    </x-header-table>
                </flux:table.columns>

                <flux:table.rows>
                    @forelse ($this->usuarios as $usuario)
                        @php
                            $nombreCompleto = trim($usuario->name . ' ' . $usuario->apellido_paterno);
                            $direccion = trim(
                                ($usuario->direccion->calle->nombre ?? '') . ' #' .
                                ($usuario->direccion->numero_exterior ?? '') . ', ' .
                                ($usuario->direccion->calle->colonia->nombre ?? ''),
                                ' #,'
                            );
                        @endphp

                        <flux:table.row :key="$usuario->id_usuario">
                            <flux:table.cell class="!px-2 w-[3.25rem] text-center tabular-nums col-hide-sm">
                                {{ $usuario->id_usuario }}
                            </flux:table.cell>

                            <flux:table.cell class="!px-2 w-[11rem]">
                                <div class="truncate" title="{{ $nombreCompleto }}">
                                    {{ $nombreCompleto }}
                                </div>
                            </flux:table.cell>

                            <flux:table.cell class="!px-2 w-[13rem] col-hide-md">
                                <div class="truncate" title="{{ $usuario->email }}">
                                    {{ $usuario->email }}
                                </div>
                            </flux:table.cell>

                            <flux:table.cell class="!px-2 w-[13rem]">
                                <div class="truncate" title="{{ $usuario->sucursal ? $usuario->sucursal->nombre : 'N/A' }}">
                                    {{ $usuario->sucursal ? $usuario->sucursal->nombre : 'N/A' }}
                                </div>
                            </flux:table.cell>

                            <flux:table.cell class="!px-2 w-[6rem] text-center">
                                <span class="inline-flex max-w-full justify-center truncate">
                                    {{ $usuario->rol_nombre ?? 'N/A' }}
                                </span>
                            </flux:table.cell>

                            <flux:table.cell class="!px-2 w-[15rem] col-hide-lg">
                                <div class="truncate" title="{{ $direccion ?: 'N/A' }}">
                                    {{ $direccion ?: 'N/A' }}
                                </div>
                            </flux:table.cell>

                            <flux:table.cell class="!px-2 w-[10rem]">
                                <div class="flex items-center justify-end gap-1 whitespace-nowrap">
                                    <flux:button
                                        size="sm"
                                        variant="ghost"
                                        icon="pencil"
                                        class="!text-azul_menu !px-1.5"
                                        title="Editar usuario"
                                        aria-label="Editar usuario"
                                        wire:click="$dispatch('preparar-edicion-usuario', { id: {{ $usuario->id_usuario }} })">
                                        Editar
                                    </flux:button>

                                    <flux:button
                                        size="sm"
                                        variant="ghost"
                                        icon="trash"
                                        class="!text-rojo_texto !px-1.5"
                                        title="Eliminar usuario"
                                        aria-label="Eliminar usuario"
                                        wire:click="$dispatch('preparar-eliminacion-usuario', { id: {{ $usuario->id_usuario }} })">
                                        <span class="hidden xl:inline ml-1">Eliminar</span>
                                    </flux:button>
                                </div>
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="7" class="text-center py-4">
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

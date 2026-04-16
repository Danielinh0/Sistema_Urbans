<?php

use Livewire\WithPagination;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use App\Models\Socio;

new class extends Component {
    use WithPagination;

    public $sortBy = 'id_socio';
    public $sortDirection = 'desc';
    public $search = '';
    public $perPage = 6;

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

    #[Computed]
    public function socios()
    {
        return Socio::query()
            ->when($this->search !== '', function ($query) {
                $query->whereRaw('LOWER(nombre) like ?', ['%' . strtolower($this->search) . '%']);
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);
    }
};
?>

<div>


    <flux:card>
        <flux:table :paginate="$this->socios">
            <flux:table.columns>
                <flux:table.column sortable :sorted="$sortBy === 'id_socio'" :direction="$sortDirection"
                    wire:click="sort('id_socio')">ID</flux:table.column>
                <flux:table.column sortable :sorted="$sortBy === 'nombre'" :direction="$sortDirection"
                    wire:click="sort('nombre')">Nombre</flux:table.column>
                <flux:table.column sortable :sorted="$sortBy === 'apellido_paterno'" :direction="$sortDirection"
                    wire:click="sort('apellido_paterno')">Apellido Paterno</flux:table.column>
                <flux:table.column sortable :sorted="$sortBy === 'apellido_materno'" :direction="$sortDirection"
                    wire:click="sort('apellido_materno')">Apellido Materno</flux:table.column>
                <flux:table.column sortable :sorted="$sortBy === 'estado'" :direction="$sortDirection"
                    wire:click="sort('estado')">Estado</flux:table.column>
                <flux:table.column sortable :sorted="$sortBy === 'fecha_de_incorporacion'" :direction="$sortDirection"
                    wire:click="sort('fecha_de_incorporacion')">Fecha de Incorporación</flux:table.column>
                <flux:table.column sortable :sorted="$sortBy === 'numero_telefonico'" :direction="$sortDirection"
                    wire:click="sort('numero_telefonico')">Teléfono</flux:table.column>
                <flux:table.column sortable :sorted="$sortBy === 'correo'" :direction="$sortDirection"
                    wire:click="sort('correo')">Correo</flux:table.column>
                <flux:table.column align="center">Acciones</flux:table.column>
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
                        <flux:table.cell>
                            {{ $socio->estado }}
                        </flux:table.cell>
                        <flux:table.cell>
                            {{ $socio->fecha_de_incorporacion }}
                        </flux:table.cell>
                        <flux:table.cell>
                            {{ $socio->numero_telefonico }}
                        </flux:table.cell>
                        <flux:table.cell>
                            {{ $socio->correo }}
                        </flux:table.cell>
                        <flux:table.cell class="flex gap-2">
                            <flux:button variant="ghost" icon="user-round-pen" class="!text-azul_menu"
                                wire:click="$dispatch('preparar-edicion-socio', { id: {{ $socio->id_socio }} })">
                                Editar
                            </flux:button>
                            <flux:button variant="ghost" icon="user-round-minus" class="!text-rojo_texto"
                                wire:click="$dispatch('preparar-eliminacion-socio', { id: {{ $socio->id_socio }} })">
                                Eliminar
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
    </flux:card>
    
    <!-- Botones de editar y eliminar -->
    <livewire:socio.manager />
</div>
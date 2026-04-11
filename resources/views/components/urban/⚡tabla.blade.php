<?php

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use App\Models\Urban;
use App\Models\Socio;

new class extends Component {
    use WithPagination;

    public $sortBy = 'id_urban';
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

    #[On('urban-creada')]
    #[On('urban-eliminada')]
    #[On('urban-actualizada')]
    public function refreshAfterCreate()
    {
        $this->resetPage();
    }

    #[Computed]
    public function urbans()
    {
        return Urban::query()
            ->with('socio:id_socio,nombre,apellido_paterno,apellido_materno')
            ->when($this->search !== '', function ($query) {
                $query->whereRaw('LOWER(codigo_urban) like ?', ['%' . strtolower($this->search) . '%']);
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate(10);
    }
};
?>

<div>
    <flux:card>
        <flux:table :paginate="$this->urbans">
            <flux:table.columns>
                <flux:table.column sortable :sorted="$sortBy === 'id_urban'" :direction="$sortDirection"
                    wire:click="sort('id_urban')" align="center">ID</flux:table.column>
                <flux:table.column sortable :sorted="$sortBy === 'codigo_urban'" :direction="$sortDirection"
                    wire:click="sort('codigo_urban')" align="center">Código</flux:table.column>
                <flux:table.column sortable :sorted="$sortBy === 'numero_asientos'" :direction="$sortDirection"
                    wire:click="sort('numero_asientos')" align="center">Número de Asientos</flux:table.column>
                <flux:table.column sortable :sorted="$sortBy === 'placa'" :direction="$sortDirection"
                    wire:click="sort('placa')" align="center">Placa</flux:table.column>
                <flux:table.column sortable :sorted="$sortBy === 'id_socio'" :direction="$sortDirection"
                    wire:click="sort('id_socio')" align="center">Socio</flux:table.column>
                <flux:table.column align="center">Acciones</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse ($this->urbans as $urban)
                    <flux:table.row :key="$urban->id_urban">
                        <flux:table.cell>
                            {{ $urban->id_urban }}
                        </flux:table.cell>
                        <flux:table.cell>
                            {{ $urban->codigo_urban }}
                        </flux:table.cell>
                        <flux:table.cell align="center">
                            {{ $urban->numero_asientos }}
                        </flux:table.cell>
                        <flux:table.cell>
                            {{ $urban->placa }}
                        </flux:table.cell>
                        <flux:table.cell>
                            {{ $urban->socio->nombre . ' ' . $urban->socio->apellido_paterno . ' ' . $urban->socio->apellido_materno }}
                        </flux:table.cell>
                        <flux:table.cell class="flex gap-2">
                            <flux:button variant="ghost" icon="pencil" class="!text-azul_menu"
                                wire:click="$dispatch('preparar-edicion-urban', { id: {{ $urban->id_urban }} })">
                                Editar
                            </flux:button>
                            <flux:button variant="ghost" icon="trash" class="!text-rojo_texto"
                                wire:click="$dispatch('preparar-eliminacion-urban', { id: {{ $urban->id_urban }} })">
                                Eliminar
                            </flux:button>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="6" class="text-center py-4 ">
                            No se encontraron urbans.
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </flux:card>
    <!-- Botones de editar y eliminar -->
    <livewire:urban.manager />
</div>
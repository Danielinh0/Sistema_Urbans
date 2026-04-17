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
            ->paginate($this->perPage);
    }
};
?>

<div>
    <livewire:barra-busqueda placeholder="Busca una urban por su código..." />
    <flux:card>
        <flux:table :paginate="$this->urbans" horizontal class="w-full">
            <flux:table.columns>
                <x-header-table sortable :sorted="$sortBy === 'id_urban'" :direction="$sortDirection"
                    wire:click="sort('id_urban')">ID</x-header-table>

                <x-header-table sortable :sorted="$sortBy === 'codigo_urban'" :direction="$sortDirection"
                    wire:click="sort('codigo_urban')">Código</x-header-table>

                <x-header-table icon="armchair" sortable :sorted="$sortBy === 'numero_asientos'"
                    :direction="$sortDirection" wire:click="sort('numero_asientos')">Asientos</x-header-table>

                <x-header-table icon="bus" sortable :sorted="$sortBy === 'placa'" :direction="$sortDirection"
                    wire:click="sort('placa')">Placa</x-header-table>

                <x-header-table icon="user" sortable :sorted="$sortBy === 'id_socio'" :direction="$sortDirection"
                    wire:click="sort('id_socio')">Socio</x-header-table>

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

                        <flux:table.cell class="flex gap-2 justify-end">
                            {{-- Botón Editar: Texto oculto en móviles (hidden), visible en tablets en adelante (md:inline)
                            --}}
                            <flux:button variant="ghost" icon="pencil" class="!text-azul_menu"
                                wire:click="$dispatch('preparar-edicion-urban', { id: {{ $urban->id_urban }} })">
                                <span class="hidden md:inline ml-1">Editar</span>
                            </flux:button>

                            {{-- Botón Eliminar --}}
                            <flux:button variant="ghost" icon="trash" class="!text-rojo_texto"
                                wire:click="$dispatch('preparar-eliminacion-urban', { id: {{ $urban->id_urban }} })">
                                <span class="hidden md:inline ml-1">Eliminar</span>
                            </flux:button>
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
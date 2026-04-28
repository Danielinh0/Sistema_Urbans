<?php

use Livewire\WithPagination;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use App\Models\Ruta;

new class extends Component {
    use WithPagination;

    public $sortBy = 'id_ruta';
    public $sortDirection = 'asc';
    public $search = '';
    public $perPage = 7;

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

    #[On('ruta-eliminada')]
    #[On('ruta-creada')]
    public function refreshAfterCreate()
    {
        $this->resetPage();
    }

    #[Computed]
    public function rutas()
    {
        return Ruta::query()
            ->when($this->search !== '', function ($query) {
                $query->whereRaw('LOWER(nombre) like ?', ['%' . strtolower($this->search) . '%']);
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);
    }

};
?>

    <div>

        <livewire:barra-busqueda placeholder="Buscar por nombre de ruta"/>

        <flux:card>
            <flux:table :paginate="$this->rutas">
                <flux:table.columns>

                    <x-header-table sortable="id_ruta" :sortBy="$sortBy" :sortDirection="$sortDirection"> ID </x-header-table>    
                        
                     <x-header-table icon="map-pinned"> Ruta </x-header-table>

                    <x-header-table icon="land-plot"> Distancia (km) </x-header-table>

                    <x-header-table icon="alarm-clock" sortable="tiempo_estimado" :sortBy="$sortBy" :sortDirection="$sortDirection"> Tiempo Est. </x-header-table>    
                    
                    <x-header-table icon="tickets" sortable="tarifa_clientes" :sortBy="$sortBy" :sortDirection="$sortDirection"> Tarifa Personas </x-header-table>    

                    <x-header-table icon="package" sortable="tarifa_paquete" :sortBy="$sortBy" :sortDirection="$sortDirection"> Tarifa Paquetes </x-header-table>   

                    <x-header-table icon="layout-grid"> Acciones </x-header-table>
                </flux:table.columns>
                <flux:table.rows>
                    @forelse ($this->rutas as $ruta)
                        <flux:table.row :key="$ruta->id_ruta">
                            <flux:table.cell>
                                {{ $ruta->id_ruta }}
                            </flux:table.cell>

                            <flux:table.cell>
                            {{ Str::limit($ruta->nombre, 30, '...') }}     
                            </flux:table.cell>

                            <flux:table.cell class="text-center!">
                                {{ $ruta->distancia }} km
                            </flux:table.cell>

                            <flux:table.cell class="pl-15">
                                {{ $ruta->tiempo_estimado }}
                            </flux:table.cell>

                            <flux:table.cell class="pl-15" variant="strong">
                                <flux:badge color="green">
                                ${{ number_format($ruta->tarifa_clientes, 2) }} 
                                </flux:badge>
                            </flux:table.cell>

                            <flux:table.cell class="pl-15" variant="strong">
                                <flux:badge color="green">
                                    ${{ number_format($ruta->tarifa_paquete, 2) }}
                                </flux:badge>
                            </flux:table.cell>
                            
                            <flux:table.cell class="flex gap-3">

                                <flux:button variant="ghost" icon="pencil" class="!text-azul_menu"
                                    wire:click="$dispatch('edicion-ruta', { id: {{ $ruta->id_ruta }} })">
                                    <span class="hidden xl:inline ml-1">Editar</span>
                                </flux:button>

                                <flux:button variant="ghost" icon="trash" class="!text-rojo_texto"
                                    wire:click="$dispatch('eliminacion-ruta', { id: {{ $ruta->id_ruta }} })">
                                    <span class="hidden xl:inline ml-1">Eliminar</span>
                                </flux:button>

                            </flux:table.cell>

                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="7" class="text-center py-4 ">
                                No se encontraron rutas.
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
                
            </flux:table>
            <flux:select size="sm" class="w-full sm:w-auto" wire:model.live="perPage">
                    <flux:select.option value="7">7</flux:select.option>
                    <flux:select.option value="14">14</flux:select.option>
                    <flux:select.option value="27">27</flux:select.option>
                    <flux:select.option value="48">48</flux:select.option>
                </flux:select>
        </flux:card>

        

        <livewire:rutas.modal />
    </div>
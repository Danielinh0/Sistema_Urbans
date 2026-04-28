<div>

    <livewire:barra-busqueda
        placeholder="Buscar por ruta o conductor" />

    <flux:card>
        <flux:table :paginate="$this->corridas">
            <flux:table.columns>

                <x-header-table sortable="id_corrida" :sortBy="$sortBy" :sortDirection="$sortDirection"> ID </x-header-table>

                <x-header-table icon="map-pinned" sortable="id_ruta" :sortBy="$sortBy" :sortDirection="$sortDirection"> Ruta </x-header-table>

                <x-header-table icon="bus"> Urban(s) </x-header-table>

                <x-header-table icon="ticket" sortable="id_usuario" :sortBy="$sortBy" :sortDirection="$sortDirection"> Conductor </x-header-table>

                <x-header-table icon="calendar" sortable="fecha" :sortBy="$sortBy" :sortDirection="$sortDirection"> Fecha </x-header-table>

                <x-header-table icon="alarm-clock" sortable="hora_salida" :sortBy="$sortBy" :sortDirection="$sortDirection"> Salida </x-header-table>

                <x-header-table icon="alarm-clock" sortable="hora_llegada" :sortBy="$sortBy" :sortDirection="$sortDirection"> Llegada </x-header-table>

                <x-header-table icon="layout-grid"> Acciones </x-header-table>
            </flux:table.columns>
            <flux:table.rows>
                @forelse ($this->corridas as $corrida)
                <flux:table.row :key="$corrida->id_corrida">
                    <flux:table.cell>
                        {{ $corrida->id_corrida }}
                    </flux:table.cell>

                    <flux:table.cell>
                        {{ Str::limit($corrida->ruta->nombre ?? 'Sin ruta', 30, '...') }}
                    </flux:table.cell>

                    <flux:table.cell class="whitespace-normal">
                        @if ($corrida->urban)
                        <div>    <flux:badge color="sky">  {{ $corrida->urban->codigo_urban }} </flux:badge> </div>
                        @else
                        <span class="text-zinc-500">Sin urban</span>
                        @endif
                    </flux:table.cell>

                    <flux:table.cell>
                        {{ $corrida->user->name ?? 'Sin conductor' }}
                    </flux:table.cell>

                    <flux:table.cell>
                        {{ $corrida->fecha ? $corrida->fecha->format('d/m/Y') : '-' }}
                    </flux:table.cell>

                    <flux:table.cell class="pl-15" variant="strong">
                        @if($corrida->hora_salida)
                        {{ $corrida->hora_salida->format('H:i') }}
                        @else
                        -
                        @endif
                    </flux:table.cell>

                    <flux:table.cell class="pl-15" variant="strong">
                        @if($corrida->hora_llegada)
                        {{ $corrida->hora_llegada->format('H:i') }}
                        @else
                        -
                        @endif
                    </flux:table.cell>

                    <flux:table.cell class="flex gap-3">

                        <flux:button variant="ghost" icon="pencil" class="!text-azul_menu"
                            wire:click="$dispatch('edicion-corrida', { id: {{ $corrida->id_corrida }} })">
                            <span class="hidden xl:inline ml-1">Editar</span>
                        </flux:button>

                        <flux:button variant="ghost" icon="trash" class="!text-rojo_texto"
                            wire:click="$dispatch('eliminacion-corrida', { id: {{ $corrida->id_corrida }} })">
                            <span class="hidden xl:inline ml-1">Eliminar</span>
                        </flux:button>

                    </flux:table.cell>

                </flux:table.row>
                @empty
                <flux:table.row>
                    <flux:table.cell colspan="8" class="text-center py-4 ">
                        No se encontraron corridas.
                    </flux:table.cell>
                </flux:table.row>
                @endforelse
            </flux:table.rows>

        </flux:table>
        <flux:select size="sm" class="w-full sm:w-auto mt-4" wire:model.live="perPage">
            <flux:select.option value="7">7</flux:select.option>
            <flux:select.option value="14">14</flux:select.option>
            <flux:select.option value="27">27</flux:select.option>
            <flux:select.option value="48">48</flux:select.option>
        </flux:select>
    </flux:card>

</div>
<div>

    <livewire:barra-busqueda
        placeholder="Buscar por ruta o conductor" />

    <flux:card class="!p-2 overflow-x-auto">
        <flux:table :paginate="$this->corridas" class="w-full text-sm compact-table" dense>
            <flux:table.columns>

                <x-header-table sortable="id_corrida" class="w-[3.25rem] text-center col-hide-sm" :sortBy="$sortBy" :sortDirection="$sortDirection"> ID </x-header-table>

                <x-header-table icon="map-pinned" sortable="id_ruta" class="w-[11rem]" :sortBy="$sortBy" :sortDirection="$sortDirection"> Ruta </x-header-table>

                <x-header-table icon="bus" class="w-[6rem] col-hide-md"> Urban </x-header-table>

                <x-header-table icon="ticket" sortable="id_usuario" class="w-[9rem]" :sortBy="$sortBy" :sortDirection="$sortDirection"> Conductor </x-header-table>

                <x-header-table icon="calendar" sortable="fecha" class="w-[6rem] col-hide-md" :sortBy="$sortBy" :sortDirection="$sortDirection"> Fecha </x-header-table>

                <x-header-table icon="alarm-clock" sortable="hora_salida" class="w-[4.5rem]" :sortBy="$sortBy" :sortDirection="$sortDirection"> Salida </x-header-table>

                <x-header-table icon="alarm-clock" sortable="hora_llegada" class="w-[4.5rem]" :sortBy="$sortBy" :sortDirection="$sortDirection"> Llegada </x-header-table>

                <x-header-table icon="wrench" class="w-[10rem]" align="center"> Acciones </x-header-table>
            </flux:table.columns>
            <flux:table.rows>
                @forelse ($this->corridas as $corrida)
                <flux:table.row :key="$corrida->id_corrida">
                    <flux:table.cell class="!px-1 w-[3.25rem] text-center tabular-nums col-hide-sm">
                        {{ $corrida->id_corrida }}
                    </flux:table.cell>

                    <flux:table.cell class="!px-2 w-[11rem]">
                        <div class="truncate" title="{{ $corrida->ruta->nombre ?? 'Sin ruta' }}">
                            {{ Str::limit($corrida->ruta->nombre ?? 'Sin ruta', 25, '...') }}
                        </div>
                    </flux:table.cell>

                    <flux:table.cell class="!px-2 w-[6rem] col-hide-md">
                        @if ($corrida->urban)
                        <flux:badge color="sky" size="sm">{{ $corrida->urban->codigo_urban }}</flux:badge>
                        @else
                        <span class="text-zinc-500">Sin urban</span>
                        @endif
                    </flux:table.cell>

                    <flux:table.cell class="!px-2 w-[9rem]">
                        <div class="truncate" title="{{ $corrida->user->name ?? 'Sin conductor' }}">
                            {{ $corrida->user->name ?? 'Sin conductor' }}
                        </div>
                    </flux:table.cell>

                    <flux:table.cell class="!px-2 w-[6rem] col-hide-md">
                        {{ $corrida->fecha ? $corrida->fecha->format('d/m/Y') : '-' }}
                    </flux:table.cell>

                    <flux:table.cell class="!px-2 w-[4.5rem] tabular-nums" variant="strong">
                        @if($corrida->hora_salida)
                        {{ $corrida->hora_salida->format('H:i') }}
                        @else
                        -
                        @endif
                    </flux:table.cell>

                    <flux:table.cell class="!px-2 w-[4.5rem] tabular-nums" variant="strong">
                        @if($corrida->hora_llegada)
                        {{ $corrida->hora_llegada->format('H:i') }}
                        @else
                        -
                        @endif
                    </flux:table.cell>

                    <flux:table.cell class="!px-2 w-[10rem]">
                        <div class="flex items-center justify-end gap-1 whitespace-nowrap">
                            <flux:button size="sm" variant="ghost" icon="pencil" class="!text-azul_menu !px-1.5"
                                wire:click="$dispatch('edicion-corrida', { id: {{ $corrida->id_corrida }} })">
                                Editar
                            </flux:button>

                            <flux:button size="sm" variant="ghost" icon="trash" class="!text-rojo_texto !px-1.5"
                                wire:click="$dispatch('eliminacion-corrida', { id: {{ $corrida->id_corrida }} })">
                                Eliminar
                            </flux:button>
                        </div>
                    </flux:table.cell>

                </flux:table.row>
                @empty
                <flux:table.row>
                    <flux:table.cell colspan="8" class="text-center py-4">
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
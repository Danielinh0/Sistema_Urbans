<div class="py-2">

    <livewire:barra-busqueda placeholder="Buscar por ruta o conductor" />

    <flux:button wire:click="prueba">
        Save changes
    </flux:button>

    <flux:card>
        <flux:table :paginate="$this->corridas" dense>
            <flux:table.columns>

<<<<<<< HEAD

=======
                <flux:table.column
                    sortable
                    :sorted="$sortBy === 'id_ruta'"
                    :direction="$sortDirection"
                    wire:click="sort('id_ruta')"
                >
                    <span class="inline-flex items-center gap-1 text-azul_menu text-sm font-semibold">
                        <flux:icon name="map-pinned" class="text-azul_menu!" /> Ruta
                    </span>
                </flux:table.column>
>>>>>>> origin/main

                <flux:table.column class="col-hide-md">
                    <span class="inline-flex items-center gap-1 text-azul_menu text-sm font-semibold">
                        <flux:icon name="bus" class="text-azul_menu!" /> Urban
                    </span>
                </flux:table.column>

                <flux:table.column
                    sortable
                    :sorted="$sortBy === 'id_usuario'"
                    :direction="$sortDirection"
                    wire:click="sort('id_usuario')"
                >
                    <span class="inline-flex items-center gap-1 text-azul_menu text-sm font-semibold">
                        <flux:icon name="ticket" class="text-azul_menu!" /> Conductor
                    </span>
                </flux:table.column>

                <flux:table.column
                    class="col-hide-md"
                    sortable
                    :sorted="$sortBy === 'fecha'"
                    :direction="$sortDirection"
                    wire:click="sort('fecha')"
                >
                    <span class="inline-flex items-center gap-1 text-azul_menu text-sm font-semibold">
                        <flux:icon name="calendar" class="text-azul_menu!" /> Fecha
                    </span>
                </flux:table.column>

                <flux:table.column
                    sortable
                    :sorted="$sortBy === 'hora_salida'"
                    :direction="$sortDirection"
                    wire:click="sort('hora_salida')"
                >
                    <span class="inline-flex items-center gap-1 text-azul_menu text-sm font-semibold">
                        <flux:icon name="alarm-clock" class="text-azul_menu!" /> Salida
                    </span>
                </flux:table.column>

                <flux:table.column
                    sortable
                    :sorted="$sortBy === 'hora_llegada'"
                    :direction="$sortDirection"
                    wire:click="sort('hora_llegada')"
                >
                    <span class="inline-flex items-center gap-1 text-azul_menu text-sm font-semibold">
                        <flux:icon name="alarm-clock" class="text-azul_menu!" /> Llegada
                    </span>
                </flux:table.column>

                @if(auth()->user()->hasAnyRole(['admin', 'gerente']))
                <flux:table.column>
                    <span class="inline-flex items-center gap-1 text-azul_menu text-sm font-semibold">
                        <flux:icon name="wrench" class="text-azul_menu!" /> Acciones
                    </span>
                </flux:table.column>
                @endif

            </flux:table.columns>
            <flux:table.rows>
                @forelse ($this->corridas as $corrida)
                <flux:table.row :key="$corrida->id_corrida">
<<<<<<< HEAD

=======
>>>>>>> origin/main

                    <flux:table.cell>
                        <div class="truncate" title="{{ $corrida->ruta->nombre ?? 'Sin ruta' }}">
                            {{ $corrida->ruta->nombre ?? 'Sin ruta' }}
                        </div>
                    </flux:table.cell>

                    <flux:table.cell class="col-hide-md">
                        @if ($corrida->urban)
                            <flux:badge color="sky">{{ $corrida->urban->codigo_urban }}</flux:badge>
                        @else
                            <span class="text-zinc-500">Sin urban</span>
                        @endif
                    </flux:table.cell>

                    <flux:table.cell>
                        <div class="truncate" title="{{ $corrida->user->name ?? 'Sin conductor' }}">
                            {{ $corrida->user->name ?? 'Sin conductor' }}
                        </div>
                    </flux:table.cell>

                    <flux:table.cell class="col-hide-md">

                     <flux:badge color="zinc">
                        {{ $corrida->fecha ? $corrida->fecha->format('d/m/Y') : '-' }}
                     </flux:badge>   
                    </flux:table.cell>

                    <flux:table.cell class="tabular-nums" variant="strong">
                        <flux:badge color="green">
                            {{ $corrida->hora_salida ? $corrida->hora_salida->format('H:i') : '-' }}
                        </flux:badge>
                    </flux:table.cell>

                    <flux:table.cell class="tabular-nums" variant="strong">
                        <flux:badge color="blue">
                            {{ $corrida->hora_llegada ? $corrida->hora_llegada->format('H:i') : '-' }}
                        </flux:badge>
                    </flux:table.cell>

<<<<<<< HEAD


                    <flux:table.cell class="!px-2 w-[10rem]">

                        <div class="flex items-center justify-end gap-1 whitespace-nowrap">
                            @can('update', $corrida)
                            <flux:button size="sm" variant="ghost" icon="pencil" class="!text-azul_menu !px-1.5"
=======
                    @can('update', $corrida)
                    <flux:table.cell>
                        <div class="flex items-center gap-1">
                            <flux:button size="sm" variant="ghost" icon="pencil" class="!text-azul_menu"
>>>>>>> origin/main
                                wire:click="$dispatch('edicion-corrida', { id: {{ $corrida->id_corrida }} })">
                            </flux:button>
<<<<<<< HEAD
                            @endcan

                            @can('delete', $corrida)
                            <flux:button size="sm" variant="ghost" icon="trash" class="!text-rojo_texto !px-1.5"
=======
                            <flux:button size="sm" variant="ghost" icon="trash" class="!text-rojo_texto"
>>>>>>> origin/main
                                wire:click="$dispatch('eliminacion-corrida', { id: {{ $corrida->id_corrida }} })">
                            </flux:button>
                            @endcan
                        </div>
                    </flux:table.cell>
<<<<<<< HEAD
=======
                    @endcan
>>>>>>> origin/main

                </flux:table.row>
                @empty
                <flux:table.row>
                    <flux:table.cell colspan="8">
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
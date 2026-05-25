<div class="py-2">

    <livewire:barra-busqueda placeholder="Buscar por ruta o conductor" :filters="[
        'estado' => [
            'label' => 'Todas',
            'options' => [
                'Programada' => 'Programada',
                'En viaje' => 'En viaje',
                'Finalizada' => 'Finalizada',
                'Cancelada' => 'Cancelada'
            ]
        ]
    ]"/>

    <flux:card>
        <flux:table :paginate="$this->corridas" dense>
            <flux:table.columns>

                <flux:table.column>
                    <span class="inline-flex items-center gap-1 text-azul_menu text-sm font-semibold">
                        <flux:icon name="map-pin-house" class="text-azul_menu!" /> Ruta
                    </span>
                </flux:table.column>

                <flux:table.column class="col-hide-md">
                    <span class="inline-flex items-center gap-1 text-azul_menu text-sm font-semibold">
                        <flux:icon name="bus" class="text-azul_menu!" /> Urban
                    </span>
                </flux:table.column>

                <flux:table.column>
                    <span class="inline-flex items-center gap-1 text-azul_menu text-sm font-semibold">
                        <flux:icon name="ticket" class="text-azul_menu!" /> Conductor
                    </span>
                </flux:table.column>

                <flux:table.column>
                    <span class="inline-flex items-center gap-1 text-azul_menu text-sm font-semibold">
                        <flux:icon name="waypoints" class="text-azul_menu!" /> Estado
                    </span>
                </flux:table.column>

                <flux:table.column
                    class="col-hide-md"
                    sortable
                    :sorted="$sortBy === 'datetime_salida'"
                    :direction="$sortDirection"
                    wire:click="sort('datetime_salida')"
                >
                    <span class="inline-flex items-center gap-1 text-azul_menu text-sm font-semibold">
                        <flux:icon name="calendar" class="text-azul_menu!" /> Fecha
                    </span>
                </flux:table.column>

                <flux:table.column>
                    <span class="inline-flex items-center gap-1 text-azul_menu text-sm font-semibold">
                        <flux:icon name="alarm-clock" class="text-azul_menu!" /> Salida
                    </span>
                </flux:table.column>

                <flux:table.column align="center">
                    <span class="inline-flex items-center gap-3 text-azul_menu text-sm font-semibold">
                        <flux:icon name="alarm-clock" class="text-azul_menu!" /> Llegada <br> aproximada
                    </span>
                </flux:table.column>

                @if(auth()->user()->hasAnyRole(['admin', 'gerente']))
                
                {{-- <flux:table.column align="center">
                    <div class="flex flex-col items-start gap-0.5 text-azul_menu">
                        <span class="inline-flex items-center gap-1 text-sm font-semibold">
                            <flux:icon name="git-compare-arrows" class="text-azul_menu!" /> 
                            Control de la corrida
                        </span>

                        <span class="text-xs font-medium">(Cambiar estado)</span>
                    </div>
                </flux:table.column> --}}

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

                    <flux:table.cell>
                        <div class="truncate" title="{{ $corrida->ruta->nombre ?? 'Sin ruta' }}">
                            {{ $corrida->ruta->nombre ?? 'Sin ruta' }}
                        </div>
                    </flux:table.cell>

                    <flux:table.cell class="col-hide-md">
                        @if ($corrida->urban)
                            <flux:badge color="cyan">{{ $corrida->urban->codigo_urban }}</flux:badge>
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
                        @php
                            $badgeColor = match ($corrida->estado) {
                                'En viaje' => 'amber',
                                'Cancelada' => 'red',
                                'Programada' => 'blue',
                                'Finalizada' => 'green',
                                default => 'zinc',
                            };
                        @endphp
                        <flux:badge color="{{ $badgeColor }}">{{ $corrida->estado }}</flux:badge>
                    </flux:table.cell>

                    <flux:table.cell class="col-hide-md">
                        {{ $corrida->datetime_salida ? $corrida->datetime_salida->format('d/m/Y') : '-' }}
                    </flux:table.cell>

                    <flux:table.cell class="tabular-nums" variant="strong">
                        <flux:badge color="emerald">
                            @if($corrida->datetime_salida)
                                {{ $corrida->datetime_salida->format('h:i') }}
                                {{ $corrida->datetime_salida->format('H') < 12 ? 'AM' : 'PM' }}
                            @else
                                -
                            @endif
                        </flux:badge>
                    </flux:table.cell>

                    <flux:table.cell align="center" class="tabular-nums" variant="strong">
                        <flux:badge color="blue">
                            @if($corrida->datetime_llegada)
                                {{ $corrida->datetime_llegada->format('h:i') }}
                                {{ $corrida->datetime_llegada->format('H') < 12 ? 'AM' : 'PM' }}
                            @else
                                -
                            @endif
                        </flux:badge>
                    </flux:table.cell>

                    @can('update', $corrida)
                    {{-- <flux:table.cell align="center">
                        <div class="flex items-center gap-2 w-full justify-center">
                            
                            <flux:button size="xs" icon="navigation"
                                        class="bg-fondo-amarillo! text-texto-fondo!
                                        hover:bg-hover-amarillo! hover:text-white! border-none! btn-animado" 
                            wire:click="$dispatch('edicion-corrida', { id: {{ $corrida->id_corrida }} })">
                                Comenzar
                            </flux:button>

                            <flux:button size="xs" variant="ghost" icon="map-pin-check" 
                                         class="bg-verde-hover! text-verde-confirmacion!
                                         hover:bg-verde-confirmacion! hover:text-verde-hover! border-none! btn-animado"
                            wire:click="$dispatch('eliminacion-corrida', { id: {{ $corrida->id_corrida }} })">
                                Finalizar 
                            </flux:button>

                            <flux:button size="xs" variant="ghost" icon="map-pin-off" 
                                         class="bg-fondo-rojo! text-texto-rojo!
                                         hover:bg-texto-rojo! hover:text-white! border-none! btn-animado"   
                            wire:click="$dispatch('eliminacion-corrida', { id: {{ $corrida->id_corrida }} })">
                                Cancelar
                            </flux:button>


                        </div>
                    </flux:table.cell> --}}

                    <flux:table.cell>
                        <div class="flex gap-4">

                            <flux:button size="sm" icon="git-compare-arrows" class="text-texto-fondo! bg-fondo-amarillo! hover:bg-hover-amarillo! hover:text-white! border-none! btn-animado"
                                wire:click="$dispatch('cambio-estado-corrida', { id: {{ $corrida->id_corrida }} })">
                            </flux:button>

                            <flux:button size="sm" variant="ghost" icon="pencil" class="bg-azul_rebajado! text-azul_menu! hover:bg-azul_menu! hover:text-white! border-none! btn-animado"
                                wire:click="$dispatch('edicion-corrida', { id: {{ $corrida->id_corrida }} })">
                            </flux:button>

                            <flux:button size="sm" variant="ghost" icon="trash" class="bg-fondo-rojo! text-texto-rojo! hover:bg-texto-rojo! hover:text-white! border-none! btn-animado"
                                wire:click="$dispatch('eliminacion-corrida', { id: {{ $corrida->id_corrida }} })">
                            </flux:button>
                            
                        </div>
                    </flux:table.cell>

                    @endcan

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

    <livewire:corrida.modal />

</div>
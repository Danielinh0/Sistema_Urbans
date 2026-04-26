<form wire:submit="save">
   <flux:card class="space-y-6">

        <div class="inline-flex gap-3 items-center">
            <flux:icon.map-plus/>
            <flux:heading size="xl">Programa una nueva corrida</flux:heading>
        </div>

        <div class="-mt-2 space-y-6">
            <flux:field>
                <flux:label class="mt-3! mb-2!" badge="Obligatorio">Para la ruta</flux:label>

                <flux:select wire:model.live="id_ruta" placeholder="Selecciona una ruta">
                    @foreach ($this->rutas as $ruta)
                            <flux:select.option value="{{ $ruta->id_ruta }}">
                                {{ $ruta->nombre }}
                            </flux:select.option>
                    @endforeach
                </flux:select>

            </flux:field>

            

            <flux:field>

                <div class="grid grid-cols-2 gap-6">
                    <flux:field>
                        <flux:label badge="Obligatorio">Urban</flux:label>
                        <flux:select wire:model.live="id_urban_actual" placeholder="Selecciona una urban">
                            @foreach ($this->urbansDisponibles as $urban)
                                <flux:select.option value="{{ $urban->id_urban }}">
                                    {{ $urban->codigo_urban }}
                                </flux:select.option>
                            @endforeach
                        </flux:select>
                    </flux:field>    

                    <flux:field>
                            <flux:label badge="Obligatorio">Conductor</flux:label>

                            <flux:select wire:model.live="id_chofer_actual" placeholder="Selecciona un chofer">
                                @foreach ($this->conductoresDisponibles as $conductor)
                                    <flux:select.option value="{{ $conductor->id_usuario }}">
                                        {{ $conductor->name }}
                                    </flux:select.option>
                                @endforeach
                            </flux:select>
                     </flux:field> 
                </div>
                
                <flux:button class="bg-azul_rebajado! text-azul_menu! disabled:cursor-not-allowed! cursor-pointer" type="button" wire:click="agregarAsignacion" 
                :disabled="!$id_urban_actual || !$id_chofer_actual">
                    Agregar </flux:button>

                <flux:error name="asignaciones" />
            </flux:field>


            @if (!empty($asignaciones))
                    <div class="mt-2 rounded-lg border border-zinc-200 dark:border-white/10 p-2 flex flex-col gap-2 items-center">
                        <div class="w-full text-center">
                            <flux:text class="text-sm" variant="strong">Urban y conductor seleccionados</flux:text>
                        </div>
                        <div class="flex flex-wrap gap-2 justify-center items-center">
                            @foreach ($asignaciones as $i => $a)
                                <div class="flex gap-1">
                                    <div class="inline-flex items-center justify-center gap-3">

                                        <x-badge-azul texto="{{ $this->urbans->firstWhere('id_urban', $a['id_urban'])?->codigo_urban ?? 'Sin urban' }}" />

                                        <x-badge-azul fuerte=true 
                                        texto="{{ $this->conductores->firstWhere('id_usuario', $a['id_usuario'])?->name ?? 'Sin chofer' }}" />

                                    </div>
                                    <div>
                                        <flux:button class="border! border-none! text-zinc-400!" icon="x" type="button" wire:click="quitarAsignacion({{ $i }})"></flux:button>
                                    </div>
                                </div>
                                
                            @endforeach
                        </div>
                    </div>
            @endif
            
            <flux:input wire:model="fecha" type="date" label="Fecha" placeholder="Seleccione una fecha" badge="Obligatorio"/>

            <div class="grid grid-cols-2 gap-6">

                <x-input-time wire="hora_salida" texto="Hora de salida" />
                <x-input-time wire="hora_llegada" texto="Hora de llegada" />
            </div>
    
        </div>
        <div class="space-y-2">
            <flux:button type="submit" variant="primary" class="w-full bg-azul_menu! border-none! cursor-pointer">Programar</flux:button>
        </div>
    </flux:card>
</form>
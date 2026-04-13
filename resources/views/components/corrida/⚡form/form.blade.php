<form wire:submit="save">
   <flux:card class="space-y-6">

        <div class="inline-flex gap-3 items-center">
            <flux:icon.map-plus/>
            <flux:heading size="xl">Programa una nueva corrida</flux:heading>
        </div>

        <div class="-mt-2 space-y-6">
            <flux:field>
                <flux:label class="!mt-3 !mb-2" badge="Obligatorio">Para la ruta</flux:label>

                <flux:select wire:model="id_ruta" placeholder="Selecciona una ruta">
                    @foreach ($this->rutas as $ruta)
                            <flux:select.option value="{{ $ruta->id_ruta }}">
                                {{ $ruta->nombre }}
                            </flux:select.option>
                    @endforeach
                </flux:select>

            </flux:field>

            <flux:field>
                <flux:label class="!mt-2 !mb-3" badge="Obligatorio">Agrega una o más urbans a la corrida</flux:label>

                 @if ($this->urbansSeleccionadas->isNotEmpty())
                    <div class="mt-2 rounded-lg border border-zinc-200 dark:border-white/10 p-2 flex flex-col pl-3 gap-2">
                        <div>
                            <flux:text>Urbans seleccionadas</flux:text>
                        </div>
                        <div class=" flex flex-wrap gap-2">
                            @foreach ($this->urbansSeleccionadas as $urban)
                                <flux:badge size="sm">{{ $urban->codigo_urban }}</flux:badge>
                            @endforeach
                        </div>
                    </div>
                @endif

                <flux:select wire:model="id_urban_actual" placeholder="Selecciona una urban">
                    @foreach ($this->urbans as $urban)
                        @if (!in_array($urban->id_urban, $id_urbans))
                            <flux:select.option value="{{ $urban->id_urban }}">
                                {{ $urban->codigo_urban }}
                            </flux:select.option>
                        @endif
                    @endforeach
                </flux:select>

                <flux:button type="button" wire:click="agregarUrban">Agregar urban </flux:button>

                
               

                <flux:error name="id_urbans" />
            </flux:field>
  
            <div class="grid grid-cols-2 gap-6">
                
                <flux:field>
                    <flux:label badge="Obligatorio">Conductor</flux:label>

                        <flux:select wire:model="id_usuario" placeholder="Conductor">
                            @foreach ($this->usuarios as $conductor)
                                    <flux:select.option value="{{ $conductor->id_usuario }}">
                                        {{ $conductor->name }}
                                    </flux:select.option>
                            @endforeach
                        </flux:select>
                     </flux:field>

                    <flux:input type="date" label="Fecha" placeholder="Seleccione una fecha" badge="Obligatorio"/>

                    <x-input-time wire="hora_llegada" texto="Hora de llegada" />
                    <x-input-time wire="hora_salida" texto="Hora de salida" />
                
            </div>
    
        </div>
        <div class="space-y-2">
            <flux:button type="submit" variant="primary" class="w-full">Programar corrida</flux:button>
        </div>
    </flux:card>
</form>
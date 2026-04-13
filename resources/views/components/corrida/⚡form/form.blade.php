<div>
   <flux:card class="space-y-6">
        <div>
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

  
            <div class="grid grid-cols-2 gap-6">
                
                <flux:field>
                    <flux:label badge="Obligatorio">Conductor</flux:label>

                        <flux:select wire:model="id_conductor" placeholder="Conductor">
                            @foreach ($this->usuarios as $conductor)
                                    <flux:select.option value="{{ $conductor->id_usuario }}">
                                        {{ $conductor->name }}
                                    </flux:select.option>
                            @endforeach
                        </flux:select>
                     </flux:field>

                    <flux:input type="date" label="Fecha" placeholder="Seleccione una fecha" badge="Obligatorio"/>
                    <flux:input label="Hora de salida" placeholder="Hora de salida" badge="Obligatorio"/>
                    <flux:input label="Hora de llegada" placeholder="Hora de llegada" badge="Obligatorio"/>\

                     <flux:field>
                        <div class="space-y-2">
                            <label for="hora_salida" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                                Hora de salida
                                
                            </label>

                            <input
                                type="time"
                                id="hora_salida"
                                name="hora_salida"
                                wire:model="hora_salida"
                                step="60"
                                class="w-full border rounded-lg block disabled:shadow-none dark:shadow-none appearance-none text-base sm:text-sm py-2 h-10 leading-[1.375rem] ps-3 pe-3 bg-white dark:bg-white/10 dark:disabled:bg-white/[7%] text-zinc-700 disabled:text-zinc-500 placeholder-zinc-400 disabled:placeholder-zinc-400/70 dark:text-zinc-300 dark:disabled:text-zinc-400 dark:placeholder-zinc-400 dark:disabled:placeholder-zinc-500 shadow-xs border-zinc-200 border-b-zinc-300/80 disabled:border-b-zinc-200 dark:border-white/10 dark:disabled:border-white/5 data-invalid:shadow-none data-invalid:border-red-500 dark:data-invalid:border-red-500 disabled:data-invalid:border-red-500 dark:disabled:data-invalid:border-red-500"
                                required
                            >
                        </div>
                    </flux:field>

                    
            </div>
    
        </div>
        <div class="space-y-2">
            <flux:button variant="primary" class="w-full">Programar corrida</flux:button>
        </div>
    </flux:card>
</div>
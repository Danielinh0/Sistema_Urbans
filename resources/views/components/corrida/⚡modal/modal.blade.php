
<div>
    <flux:modal name="modal-editar-corrida" class="w-8/10 xl:w-[60%] xl:p-10" x-on:close="Livewire.dispatch('reset-form')">
        @if($corrida)
        <div class="inline-flex gap-3 items-center mb-6">
            <flux:icon.map-plus />
            <flux:heading size="xl">Editar Corrida</flux:heading>
        </div>
        
        <flux:card class="space-y-6">
            <div class="-mt-2 space-y-6">
                <flux:field>
                    <flux:label class="mt-3! mb-2!" badge="Obligatorio">Para la ruta</flux:label>
                    <flux:select wire:model.live="id_ruta" placeholder="Selecciona una ruta">
                        @foreach($this->rutas() as $ruta)
                            <flux:select.option value="{{ $ruta->id_ruta }}">{{ $ruta->nombre }}</flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:error name="id_ruta" />
                </flux:field>

                <div class="grid grid-cols-2 gap-6">
                    <flux:input wire:model.live="fecha" type="date" label="Fecha de salida" placeholder="Seleccione una fecha" badge="Obligatorio" />
                    <x-input-time wire="datetime_salida" texto="Hora de salida" />
                </div>

                <div class="grid grid-cols-2 gap-6">
                    <flux:input wire:model="fecha_llegada" disabled type="date" label="Fecha de llegada" placeholder="Seleccione una fecha" />
                    <x-input-time :requerido="false" :disabled="true" wire="datetime_llegada" texto="Hora de llegada" />
                </div>

                <div class="grid grid-cols-2 gap-6">
                    <flux:field>
                        <flux:label badge="Obligatorio">Urban</flux:label>
                        <flux:select wire:model="id_urban" placeholder="Selecciona una urban" :disabled="!$this->fecha || !$this->datetime_salida || !$this->id_ruta">
                            @foreach($this->urbans_disponibles as $urban)
                                <flux:select.option value="{{ $urban->id_urban }}">{{ $urban->codigo_urban }} - {{ $urban->placa }}</flux:select.option>
                            @endforeach
                        </flux:select>
                    </flux:field>

                    <flux:field>
                        <flux:label badge="Obligatorio">Conductor</flux:label>
                        <flux:select wire:model="id_usuario" placeholder="Selecciona un chofer" :disabled="!$this->fecha || !$this->datetime_salida || !$this->id_ruta">
                            @foreach($this->choferes_disponibles as $conductor)
                                <flux:select.option value="{{ $conductor->id_usuario }}">{{ $conductor->name }}</flux:select.option>
                            @endforeach
                        </flux:select>
                    </flux:field>
                </div>
            </div>
        </flux:card>

        <div class="mt-8 space-y-2">
            <flux:button wire:click="update" variant="primary" icon="calendar-clock" 
                class="w-full bg-azul_rebajado! text-azul_menu!
                hover:bg-azul_menu! hover:text-white! hover:-translate-y-1/4
                transition delay-130 duration-300 ease-in-out cursor-pointer border-none!">
                Guardar Cambios
            </flux:button>
        </div>
        @endif
    </flux:modal>

    <flux:modal name="modal-estado-corrida" class="w-[90%] md:w-[45%] xl:w-[35%] xl:p-8">
        @if($corrida)
        <div class="flex flex-col gap-4">
            
            <div class="inline-flex gap-3 items-center">
                <flux:icon.git-compare-arrows class="text-azul_menu!" />
                <flux:heading size="xl" class="text-azul_menu! font-bold">Control de la corrida</flux:heading>
            </div>
             
            <div>
                <flux:text>
                    Selecciona el nuevo estado para la corrida asignada a <b>{{ $corrida->user->name ?? 'conductor' }}</b> en la ruta <b>{{ $corrida->ruta->nombre ?? 'Sin ruta' }}</b>.
                </flux:text>
            </div>
            
            <div class="flex flex-col gap-4 mt-4">
                <div class="flex items-center gap-4 w-full justify-center">
                    <flux:button size="sm" icon="navigation"
                                class="bg-fondo-amarillo! text-texto-fondo!
                                hover:bg-hover-amarillo! hover:text-white! border-none! btn-animado" 
                    wire:click="actualizarEstado('En viaje')">
                        Comenzar
                    </flux:button>

                    <flux:button size="sm" variant="ghost" icon="map-pin-check" 
                                 class="bg-verde-hover! text-verde-confirmacion!
                                 hover:bg-verde-confirmacion! hover:text-verde-hover! border-none! btn-animado"
                    wire:click="actualizarEstado('Finalizada')">
                        Finalizar 
                    </flux:button>

                    <flux:button size="sm" variant="ghost" icon="map-pin-off" 
                                 class="bg-fondo-rojo! text-texto-rojo!
                                 hover:bg-texto-rojo! hover:text-white! border-none! btn-animado"   
                    wire:click="actualizarEstado('Cancelada')">
                        Cancelar
                    </flux:button>
                </div>
            </div>
          
        </div>
        @endif
    </flux:modal>

    <flux:modal name="modal-eliminar-corrida" class="w-[40%] md:min-w-[22rem]">
        @if($corrida)
        <div class="space-y-6">
            <flux:heading size="lg">Eliminar Corrida</flux:heading>
            <flux:text>
                ¿Estás seguro de que deseas eliminar esta corrida asiganada a <b>{{ $corrida->user->name ?? 'conductor' }}</b>?
            </flux:text>
            <div class="flex gap-2">
                <flux:spacer />
                <flux:modal.close>
                    <flux:button variant="ghost">Cancelar</flux:button>
                </flux:modal.close>
                <flux:button wire:click="delete" variant="danger">Eliminar</flux:button>
            </div>
        </div>
        @endif
    </flux:modal>
</div>
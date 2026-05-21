<div>
    <div class="space-y-2">
        
        <div class="grid grid-cols-2 gap-3 mb-5 xs:grid-cols-2">
            
            <flux:field>
                <flux:label class="mt-3! mb-2!" badge="Obligatorio">Sucursal de Salida</flux:label>

                <flux:select wire:model.live="sucursal_salida" placeholder="Salida">
                    @foreach ($this->sucursales() as $sucursal)
                            <flux:select.option value="{{ $sucursal->id_sucursal }}">
                                {{ $sucursal->nombre }}
                            </flux:select.option>
                    @endforeach
                </flux:select>
            </flux:field>

            <flux:field>
                <flux:label class="mt-3! mb-2!" badge="Obligatorio">Sucursal de Llegada</flux:label>

                <flux:select wire:model.live="sucursal_llegada" placeholder="LLegada">
                    @foreach ($this->sucursales() as $sucursal)
                            <flux:select.option value="{{ $sucursal->id_sucursal }}">
                                {{ $sucursal->nombre }}
                            </flux:select.option>
                    @endforeach
                </flux:select>
            </flux:field>
            

        </div>
        
        <flux:field>
            <flux:label badge="Obligatorio">Nombre de la ruta</flux:label>

            <flux:input
                wire:model.live.blur="nombre"
                x-on:blur="$wire.touchField('nombre')"
                x-on:keydown="!/^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s\-]$/.test($event.key)
                && !['Backspace','Delete','Tab','ArrowLeft','ArrowRight'].includes($event.key)
                && $event.preventDefault()"
                icon:trailing="a-large-small"
                type="text"
                placeholder="Ej: Tepic - San Blas" />
            <flux:error name="nombre" />
        </flux:field>

        <div class="grid grid-cols-1  gap-6 mt-7 xs:grid-cols-2 ">
            <flux:field>
                <flux:label badge="Obligatorio">Distancia (km)</flux:label>

                <flux:input
                    wire:model.live.blur="distancia"
                    x-on:blur="$wire.touchField('distancia')"
                    type="number"
                    min="0"
                    icon:trailing="land-plot"
                    placeholder="Ej: 45.5" />
                <flux:error name="distancia" />
            </flux:field>
            {{-- <div>
                <x-input-time wire="tiempo_estimado" texto="Tiempo" />
            </div> --}}

            <flux:field>
                <flux:label badge="Obligatorio">Tiempo Estimado</flux:label>

                <flux:input
                    wire:model.live.blur="tiempo_estimado"
                    x-on:blur="$wire.touchField('tiempo_estimado')"
                    icon:trailing="clock-8"
                    placeholder="Ej: 01:30" />
                <flux:error name="tiempo_estimado" />
            </flux:field>

            <flux:field>
                <flux:label badge="Obligatorio">Tarifa para personas ($MXN)</flux:label>

                <flux:input
                    wire:model.live.blur="tarifa_clientes"
                    x-on:blur="$wire.touchField('tarifa_clientes')"
                    type="number"
                    min="0"
                    icon:trailing="book-user"
                    placeholder="Ej: 150.00" />
                <flux:error name="tarifa_clientes" />
            </flux:field>
            <flux:field>
                <flux:label badge="Obligatorio">Tarifa para paquetes ($MXN)</flux:label>

                <flux:input
                    wire:model.live.blur="tarifa_paquete"
                    x-on:blur="$wire.touchField('tarifa_paquete')"
                    type="number"
                    min="0"
                    icon:trailing="package"
                    placeholder="Ej: 80.00" />
                <flux:error name="tarifa_paquete" />
            </flux:field>
        </div>

    </div>
</div>
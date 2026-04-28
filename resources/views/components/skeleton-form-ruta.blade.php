<div>
    <div class="space-y-3">
        <flux:field>
            <flux:label badge="Obligatorio">Nombre de la ruta</flux:label>

            <flux:input
                wire:model.live.blur="nombre"
                x-on:blur="$wire.touchField('nombre')"
                x-on:keydown="!/^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]$/.test($event.key)
                && !['Backspace','Delete','Tab','ArrowLeft','ArrowRight'].includes($event.key)
                && $event.preventDefault()"
                icon:trailing="a-large-small"
                type="text" />
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
                    icon:trailing="land-plot" />
                <flux:error name="distancia" />
            </flux:field>
            <div>
                <x-input-time wire="tiempo_estimado" texto="Tiempo" />
            </div>
            <flux:field>
                <flux:label badge="Obligatorio">Tarifa para personas ($MXN)</flux:label>

                <flux:input
                    wire:model.live.blur="tarifa_clientes"
                    x-on:blur="$wire.touchField('tarifa_clientes')"
                    type="number"
                    min="0"
                    icon:trailing="book-user" />
                <flux:error name="tarifa_clientes" />
            </flux:field>
            <flux:field>
                <flux:label badge="Obligatorio">Tarifa para paquetes ($MXN)</flux:label>

                <flux:input
                    wire:model.live.blur="tarifa_paquete"
                    x-on:blur="$wire.touchField('tarifa_paquete')"
                    type="number"
                    min="0"
                    icon:trailing="package" />
                <flux:error name="tarifa_paquete" />
            </flux:field>
        </div>

    </div>
</div>
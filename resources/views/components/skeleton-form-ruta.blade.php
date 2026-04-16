
<div>
    <div class="space-y-3">
        <flux:field>
            <flux:label badge="Obligatorio">Nombre de la ruta</flux:label>
            
            <flux:input wire:model.live.blur="nombre" icon:trailing="a-large-small" type="text" />
            <flux:error name="nombre" />
        </flux:field>

        <div class="grid grid-cols-1  gap-6 mt-7 xs:grid-cols-2 ">
            <flux:field>
                <flux:label badge="Obligatorio">Distancia</flux:label>

                <flux:input wire:model.live.blur="distancia" icon:trailing="land-plot" />
                <flux:error name="distancia" />
            </flux:field>
            <div>
                <x-input-time wire="tiempo_estimado" texto="Tiempo" />
            </div>
            <flux:field>
                <flux:label badge="Obligatorio">Tarifa para personas</flux:label>

                <flux:input wire:model.live.blur="tarifa_clientes" icon:trailing="book-user" />
                <flux:error name="tarifa_clientes" />
            </flux:field>
            <flux:field>
                <flux:label badge="Obligatorio">Tarifa para paquetes</flux:label>

                <flux:input wire:model.live.blur="tarifa_paquete" icon:trailing="package" />
                <flux:error name="tarifa_paquete" />
            </flux:field>
        </div>
       
    </div>        
</div>
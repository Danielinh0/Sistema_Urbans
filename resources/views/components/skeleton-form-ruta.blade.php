
<div>
    <div class="space-y-2">
        <div>
            <flux:input wire:model.live.blur="nombre" icon:trailing="a-large-small" type="text" label="Nombre de la ruta"  description:trailing="No se debe dejar en blanco, siendo de almenos 20 caracteres manejandose por la nomenclatura 'Ruta - Destino'"/>
            @error('nombre') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>
        <div>
            <flux:input wire:model.live.blur="distancia" icon:trailing="land-plot"  label="Distancia"  description:trailing="Ingrese la distancia de la ruta en kilómetros"/>
            @error('distancia') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>
        <div>
            <flux:input wire:model.live.blur="tiempo_estimado" icon:trailing="clock-fading" label="Tiempo Estimado de Viaje"  description:trailing="Tiempo estimado para completar la ruta en formato HH:MM"/>
            @error('tiempo_estimado') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>
        <div>
            <flux:input wire:model.live.blur="tarifa_clientes" icon:trailing="book-user" label="Tarifa para personas"  description:trailing="Ingrese la tarifa para personas en la ruta "/>
            @error('tarifa_clientes') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>
        <div>
            <flux:input wire:model.live.blur="tarifa_paquete" icon:trailing="package" label="Tarifa para paquetes"  description:trailing="Ingrese la tarifa para paquetes en la ruta "/>
            @error('tarifa_paquete') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>
    </div>        
</div>
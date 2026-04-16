
<div>
    <div class="space-y-7">
        <div>
            <flux:input badge="Obligatorio" wire:model.live.blur="nombre" icon:trailing="a-large-small" type="text" label="Nombre de la ruta"/>
            @error('nombre') <span class="text-red-500 text-sm ">{{ $message }}</span> @enderror
        </div>

        <div class=" grid grid-cols-2 gap-6">
             <div>
                <flux:input badge="Obligatorio" wire:model.live.blur="distancia" icon:trailing="land-plot"  label="Distancia" />
                @error('distancia') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
            <div>
                <x-input-time wire="tiempo_estimado" texto="Hora de llegada" />
            </div>
            <div>
                <flux:input badge="Obligatorio" wire:model.live.blur="tarifa_clientes" icon:trailing="book-user" label="Tarifa para personas" />
                @error('tarifa_clientes') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
            <div>
                <flux:input badge="Obligatorio"  wire:model.live.blur="tarifa_paquete" icon:trailing="package" label="Tarifa para paquetes"  />
                @error('tarifa_paquete') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
        </div>
       
    </div>        
</div>
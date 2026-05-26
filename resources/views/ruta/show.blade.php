<x-layouts::app :title="__('Detalle Ruta')">
     <section class="flex flex-col gap-5 px-5 pt-2">
        
        @if(request()->has('reasignar'))
            <div x-data="{ show: true }" x-show="show" x-collapse>
                <flux:callout icon="map-pin-minus" color="amber" class="mt-4">
                    <flux:callout.heading>Reasignación de corridas obligatoria</flux:callout.heading>
                    <flux:callout.text>
                        Para poder editar o desactivar esta ruta, primero debes cancelar o reasignar otra ruta a las siguientes corridas programadas.
                    </flux:callout.text>
                    <x-slot name="controls">
                        <flux:button icon="x-mark" variant="ghost" x-on:click="show = false" />
                    </x-slot>
                </flux:callout>
            </div>
        @endif

        <header class="flex flex-col justify-between items-center md:flex-row">
            <div>
                <x-heading :icono="'map-pinned'" texto="Información de la ruta {{ $ruta->nombre }}" />
            </div>
        </header>

        <section class="flex flex-col rounded-lg p-6 gap-1 shadow-sm -mt-3">

            <div class ="flex items-center gap-3 p-4">
                    <flux:icon name="info" class="inline size-9 text-azul_menu" />
                    <flux:text class="text-lg xs:text-4xl font-medium !text-azul_menu ">Estado de la ruta</flux:text>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6 w-full">
                
                <flux:modal.trigger name="corridas">
                    <x-card icono="calendar-clock" fondo_icono="bg-[#ccf6fc]!" color_icono="text-[#005f78]!" contador="{{ $corridasProgramadas }}" texto="Corridas programadas" />
                </flux:modal.trigger>


                <x-card icono="map-pinned" fondo_icono="bg-[#cdf9e3]!" color_icono="text-[#016630]!" contador="{{ $corridasFinalizadas }}" texto="Corridas finalizadas" />

            </div>
        </section>

        <section>
            <livewire:ruta.detalle.tabla :idRuta="$ruta->id_ruta" />
        </section>

    </section> 
    
    <div>
         <flux:modal name="corridas" class="!max-w-5xl p-9"> 
            <div class="flex flex-col gap-3"> 
                <div class ="flex items-center gap-3 p-3">
                    <flux:icon name="map" class="inline size-9 text-azul_menu" />
                    <flux:text class="text-sm xs:text-3xl font-medium !text-azul_menu ">Corridas programadas para {{ $ruta->nombre }} </flux:text>
                </div>
                <livewire:ruta.detalle.corridas :idRuta="$ruta->id_ruta" />
            </div>
        </flux:modal>
    </div>

    <livewire:corrida.modal />
</x-layouts::app>

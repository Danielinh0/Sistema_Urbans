<x-layouts::app :title="__('Corridas')">

    <main class="flex flex-col gap-5 px-4 pt-2">
        <header class="flex flex-col justify-between items-center md:flex-row">
            <div>
                <x-heading :icono="'map'" texto="Corridas" />
            </div>

            @if(auth()->user()->hasAnyRole(['gerente', 'admin']))
            <div>
                <flux:modal.trigger name="crear-corrida">
                    <flux:button icon="map-plus"
                        class="bg-azul_rebajado! cursor-pointer text-azul_menu! hover:bg-azul_menu!
                    hover:text-white! transition delay-150 duration-300 ease-in-out hover:-translate-y-1/4 hover:scale-110">Crear
                        una nueva corrida</flux:button>
                </flux:modal.trigger>
            </div>
            @endif
        </header>
        
        <section class="flex"> 
            <div class= "flex gap-6 rounded-xl items-center shadow-md p-7">
                
                <div class= "rounded-full bg-[#d6eaf8]! p-2 flex items-center gap-2">
                    <flux:icon name="calendar-clock" class="text-[#3498db]! size-10!" /> 
                </div>

                <div>
                    <flux:text class="text-3xl font-bold text-[#404040]">{{ $corridasEnProceso }}</flux:text>
                    <flux:text class="text-xl text-[#808080]">Corridas programadas</flux:text>
                </div>
            </div>

            <x-card icono="calendar-clock" fondo_icono="bg-[#d6eaf8]!" color_icono="text-[#3498db]!" contador="{{ $corridasEnProceso }}" texto="Corridas programadas" />


        </section>
        
        <section>
            <livewire:corrida.tabla />
        </section>
    </main>

    <flux:modal name="crear-corrida" class="w-[50%] p-10">
        <div>
            <livewire:corrida.form />
        </div>
    </flux:modal>

</x-layouts::app>

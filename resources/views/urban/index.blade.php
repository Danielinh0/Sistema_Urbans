<x-layouts::app :title="__('Urbans')">
    <section class="flex flex-col gap-5 px-5 pt-2">
        
        <header class="flex flex-col justify-between items-center md:flex-row">
            <div>
                <x-heading :icono="'bus'" texto="Gestion y control de Urbans" />
            </div>

            @if(auth()->user()->hasAnyRole(['gerente', 'admin']))
            <div>
                <flux:modal.trigger name="edit-urban">
                    <flux:button icon="bus"
                        class="bg-azul_rebajado! cursor-pointer text-azul_menu! hover:bg-azul_menu!
                    hover:text-white! transition delay-150 duration-300 ease-in-out hover:-translate-y-1/4 hover:scale-110">Crear
                        una nueva urban
                    </flux:button>
                </flux:modal.trigger>
            </div>
            @endif
        </header>

        <section class="flex flex-col rounded-lg p-6 gap-1 shadow-sm -mt-3">

            <div class ="flex items-center gap-3 p-4">
                    <flux:icon name="sunrise" class="inline size-9 text-azul_menu" />
                    <flux:text class="text-lg xs:text-4xl font-medium !text-azul_menu ">Estado de las urbans</flux:text>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6 w-full">
                
                <x-card icono="van" fondo_icono="bg-[#cdf9e3]!" color_icono="text-[#016630]!" contador="{{ $UrbansActivas }}" texto="Urbans disponibles" />

                <x-card icono="wrench" fondo_icono="bg-[#fcebdb]!" color_icono="text-[#f39c12]!" contador="{{ $UrbansMantenimiento }}" texto="Urbans en mantenimiento" />

                <x-card icono="info" fondo_icono="bg-[#ccf6fc]!" color_icono="text-[#005f78]!" contador="{{ $UrbansFueraDeServicio }}" texto="Urbans fuera de servicio" />
    
                <x-card icono="trending-down" fondo_icono="bg-[#f1e1f7]!" color_icono="text-[#bb6bd9]!" contador="{{ $UrbansInactivas }}" texto="Urbans inactivas" />

            </div>
        </section>

        <section>
            <livewire:urban.tabla />
        </section>


        <div>
            <flux:modal name="edit-urban" class="w-[50%] p-10" x-on:close="Livewire.dispatch('reset-form')">
                <div>
                    <flux:heading class="!text-xl !font-bold" size="lg">Crear una nueva urban</flux:heading>
                </div>
                <livewire:urban.form />
            </flux:modal>
        </div>
    </section> 


</x-layouts::app>
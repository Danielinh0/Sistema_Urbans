<x-layouts::app :title="__('ruta')">

    <section class="flex flex-col gap-6 px-9 pt-2">


        <div class="flex flex-col justify-between items-center
                     md:flex-row ">

            <div>
                <x-heading :icono="'map-pin-house'" texto="Rutas de viaje" />
            </div>

            <div class="pr-2">
                <flux:modal.trigger name="edit-profile">
                    <flux:button class="bg-azul_rebajado! cursor-pointer text-azul_menu! hover:bg-azul_menu! hover:text-white!
                    transition delay-150 duration-300 ease-in-out hover:-translate-y-1/4 hover:scale-110"
                        icon="map-pin-plus"> Nueva ruta </flux:button>
                </flux:modal.trigger>
            </div>
        </div>

        <div>
            <livewire:rutas.tabla />
        </div>


    </section>

    <flux:modal name="edit-profile" class="w-8/10 xl:w-[60%] xl:p-10" x-on:close="Livewire.dispatch('reset-form')">
        <div>
            <flux:heading class="!text-xl !font-bold" size="lg">Crea una nueva ruta</flux:heading>
        </div>
        <livewire:rutas.form />
    </flux:modal>

</x-layouts::app>
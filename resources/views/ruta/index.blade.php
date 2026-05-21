<x-layouts::app :title="__('ruta')">

    <section class="flex flex-col gap-6 px-6 pt-2">

        <div class="flex flex-col justify-between items-center  md:flex-row ">

            <div>
                <x-heading :icono="'map-pin-house'" texto="Rutas de viaje" />
            </div>

            @if(auth()->user()->hasAnyRole(['gerente', 'admin']))
            <div class="pr-2">
                <flux:modal.trigger name="edit-profile">
                    <flux:button class="
                     bg-azul_rebajado!  text-azul_menu!
                     hover:bg-azul_menu! hover:text-white! hover:-translate-y-1/4 hover:scale-110
                       transition delay-150 duration-300 ease-in-out cursor-pointer border-none!"
                        icon="map-pin-plus"> Nueva ruta </flux:button>
                </flux:modal.trigger>
            </div>
            @endif
        </div>

        <div class="px-3" >
            <livewire:rutas.tabla />
        </div>

    </section>

    <flux:modal name="edit-profile" class="w-8/10 xl:w-[60%] xl:p-6" x-on:close="Livewire.dispatch('reset-form')">
        <div class="pl-4">
            <flux:heading class="!text-2xl !font-bold" >Crea una nueva ruta</flux:heading>
        </div>
        <livewire:rutas.form />
    </flux:modal>

</x-layouts::app>
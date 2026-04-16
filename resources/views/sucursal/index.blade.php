<x-layouts::app :title="__('sucursal')">
    {{-- <div>
        <flux:modal.trigger name="add-sucursal">
            <flux:button icon="squares-plus">Añadir sucursal</flux:button>
        </flux:modal.trigger>
        <livewire:sucursal.table />
        <flux:modal name="add-sucursal">
            <div>
                <flux:header class="!text-xl !font-bold" size="lg">Añadir nueva sucursal</flux:header>
            </div>
            <livewire:sucursal.form />
        </flux:modal>
    </div> --}}


     <section class="flex flex-col gap-6 px-9 pt-2">

            
        <div class="flex flex-col justify-between items-center
                     md:flex-row ">

            <div >
                <x-heading :icono="'building-2'" texto="Sucursales" />
            </div>

            <div class="pr-2">
                <flux:modal.trigger name="Sucursal-form">
                    <flux:button class="bg-azul_rebajado! cursor-pointer text-azul_menu! hover:bg-azul_menu! hover:text-white!
                    transition delay-150 duration-300 ease-in-out hover:-translate-y-1/4 hover:scale-110" 
                    icon="house-plus"> Nueva sucursal </flux:button>
                </flux:modal.trigger>
            </div>
        </div>
                
            <div>
                <livewire:sucursal.table />
            </div>



            <flux:modal name="Sucursal-form" class="w-8/10 xl:w-[60%] xl:p-10">
                <div>
                    <flux:heading class="!text-xl !font-bold" size="lg">Crea una nueva sucursal</flux:heading>
                </div>
                    <livewire:sucursal.form />
            </flux:modal>


</x-layouts::app>
<x-layouts::app :title="__('Urbans')">
    <section class="flex flex-col gap-6 px-9 pt-2">
        <div class="flex flex-col justify-between items-center md:flex-row">
            <div>
                <x-heading :icono="'bus'" texto="Urbans" />
            </div>
            <div>
                <flux:modal.trigger name="edit-urban">
                    <flux:button icon="bus"
                        class="bg-azul_rebajado! cursor-pointer text-azul_menu! hover:bg-azul_menu!
                    hover:text-white! transition delay-150 duration-300 ease-in-out hover:-translate-y-1/4 hover:scale-110">Crear
                        una nueva urban</flux:button>
                </flux:modal.trigger>
            </div>
        </div>
        <div>
            <livewire:urban.tabla />
        </div>
        <div>
            <flux:modal name="edit-urban" class="w-[50%] p-10">
                <div>
                    <flux:heading class="!text-xl !font-bold" size="lg">Crear una nueva urban</flux:heading>
                </div>
                <livewire:urban.form />
            </flux:modal>
        </div>
    </section> 


</x-layouts::app>
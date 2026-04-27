<x-layouts::app :title="__('cliente')">
    <section class="flex flex-col gap-6 px-9 pt-2">
        <div class="flex flex-col justify-between items-center md:flex-row">
            <div>
                <x-heading :icono="'users'" texto="Clientes" />
            </div>

            <div>
                <flux:modal.trigger name="add-cliente">
                    <flux:button icon="user-plus" class="bg-azul_rebajado! cursor-pointer text-azul_menu! hover:bg-azul_menu! hover:text-white!
                    transition delay-150 duration-300 ease-in-out hover:-translate-y-1/4 hover:scale-110">
                        Crea un nuevo cliente
                    </flux:button>
                </flux:modal.trigger>
            </div>
        </div>

        <div>
            <livewire:cliente.table />
        </div>
    </section>

    <flux:modal name="add-cliente" class="w-[50%] p-10">
        <div>
            <flux:heading class="!text-xl !font-bold" size="lg">Crea un nuevo cliente</flux:heading>
        </div>
        <livewire:cliente.form />
    </flux:modal>
</x-layouts::app>

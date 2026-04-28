<x-layouts::app :title="__('Usuarios')">
    <section class="flex flex-col gap-3 px-4 pt-2">
        <div class="flex flex-col justify-between items-center md:flex-row">
            <div>
                <x-heading :icono="'users'" texto="Usuarios" />
            </div>

            @if(auth()->user()->hasAnyRole(['gerente', 'admin']))
            <div>
                <flux:modal.trigger name="add-usuario">
                    <flux:button icon="squares-plus"
                        class="bg-azul_rebajado! cursor-pointer text-azul_menu! hover:bg-azul_menu!
                    hover:text-white! transition delay-150 duration-300 ease-in-out hover:-translate-y-1/4 hover:scale-110">Añadir
                        usuario</flux:button>
                </flux:modal.trigger>
            </div>
            @endif
        </div>
        <div>
            <livewire:usuario.table />
        </div>
        <div>
            <flux:modal name="add-usuario" class="w-[50%] p-10">
                <div>
                    <flux:heading class="!text-xl !font-bold" size="lg">Añadir nuevo usuario</flux:heading>
                </div>
                <livewire:usuario.form />
            </flux:modal>
        </div>
    </section>
</x-layouts::app>

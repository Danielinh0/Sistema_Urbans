<x-layouts::app :title="__('Corridas')">

    <section class="flex flex-col gap-3 px-4 pt-2">
        <div class="flex flex-col justify-between items-center md:flex-row">
            <div>
                <x-heading :icono="'map'" texto="Corridas" />
            </div>

            @if(auth()->user()->hasAnyRole(['gerente', 'admin']))
            <div>
                <flux:modal.trigger name="crear-corrida">
                    <flux:button icon="square-plus"
                        class="bg-azul_rebajado! cursor-pointer text-azul_menu! hover:bg-azul_menu!
                    hover:text-white! transition delay-150 duration-300 ease-in-out hover:-translate-y-1/4 hover:scale-110">Crear
                        una nueva corrida</flux:button>
                </flux:modal.trigger>
            </div>
            @endif
        </div>
        <div>
            <livewire:corrida.tabla />
        </div>
    </section>

    <flux:modal name="crear-corrida" class="w-[50%] p-10">
        <div>
            <livewire:corrida.form />
        </div>
    </flux:modal>

</x-layouts::app>

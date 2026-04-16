<x-layouts::app :title="__('corrida')">
    
    <flux:heading size="xl">Corridas</flux:heading>

    <section class="flex flex-col gap-6 mt-6">
            <div>
                <flux:modal.trigger name="crear-corrida">
                    <flux:button icon="square-plus">Crea una nueva corrida </flux:button>
                </flux:modal.trigger>
            </div>

            <div>
                {{-- <livewire:corrida.tabla /> --}}
            </div>
    </section>

    <flux:modal name="crear-corrida" class="w-[50%] p-12">
        <div>
            <livewire:corrida.form />
        </div>
    </flux:modal>

</x-layouts::app>

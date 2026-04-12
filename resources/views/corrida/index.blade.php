<x-layouts::app :title="__('corrida')">
    <section class="flex ">

        <div class="">
            <flux:modal.trigger name="crear-corrida">
                <flux:button icon="square-plus">Crea una nueva ruta </flux:button>
            </flux:modal.trigger>
            <livewire:rutas.tabla />


            <flux:modal name="crear-corrida" class="w-[50%] p-10">
                <div>
                    <flux:heading class="!text-xl !font-bold" size="lg">Programa una nueva corrida</flux:heading>
                </div>
                <livewire:rutas.form />
            </flux:modal>
        </div>

    </section>
</x-layouts::app>

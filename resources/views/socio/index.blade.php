<x-layouts::app :title="__('Socio')">
    <section class="flex ">
        <div class="">
            <flux:modal.trigger name="edit-socio">
                <flux:button icon="user-plus">Crea un nuevo socio </flux:button>
            </flux:modal.trigger>
            <livewire:socio.tabla />


            <flux:modal name="edit-socio" class="w-[50%] p-10">
                <div>
                    <flux:heading class="!text-xl !font-bold" size="lg">Crea un nuevo socio</flux:heading>
                </div>
                <livewire:socio.form />
            </flux:modal>
        </div>

    </section>
</x-layouts::app>
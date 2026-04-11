<x-layouts::app :title="__('Urbans')">
    <section class="flex ">
        <div>
            <flux:modal.trigger name="edit-urban">
                <flux:button icon="circle-plus">Crear una nueva urban</flux:button>
            </flux:modal.trigger>
            <livewire:urban.tabla />
            <flux:modal name="edit-urban" class="w-[50%] p-10">
                <div>
                    <flux:heading class="!text-xl !font-bold" size="lg">Crear una nueva urban</flux:heading>
                </div>
                <livewire:urban.form />
            </flux:modal>
        </div>
    </section>
</x-layouts::app>
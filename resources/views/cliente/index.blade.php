<x-layouts::app :title="__('cliente')">
    <div>
        <flux:modal.trigger name="add-cliente">
            <flux:button icon="squares-plus">Añadir cliente</flux:button>
        </flux:modal.trigger>
        <livewire:cliente.table />
        <flux:modal name="add-cliente">
            <div>
                <flux:header class="!text-xl !font-bold" size="lg">Añadir nuevo cliente</flux:header>
            </div>
            <livewire:cliente.form />
        </flux:modal>
    </div>
</x-layouts::app>

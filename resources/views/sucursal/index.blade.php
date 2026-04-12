<x-layouts::app :title="__('sucursal')">
    <div>
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
    </div>
</x-layouts::app>
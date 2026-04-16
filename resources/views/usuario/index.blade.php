<x-layouts::app :title="__('Usuarios')">
    <div>
        <flux:modal.trigger name="add-usuario">
            <flux:button icon="squares-plus">Añadir usuario</flux:button>
        </flux:modal.trigger>
        <livewire:usuario.table />
        <flux:modal name="add-usuario">
            <div>
                <flux:header class="!text-xl !font-bold" size="lg">Añadir nuevo usuario</flux:header>
            </div>
            <livewire:usuario.form />
        </flux:modal>
    </div>
</x-layouts::app>

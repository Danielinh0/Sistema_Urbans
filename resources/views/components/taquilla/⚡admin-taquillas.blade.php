<?php

use Livewire\Component;

new class extends Component {
    public function with(): array
    {
        return [];
    }
};
?>

<div class="p-6 space-y-6">
    <div class="flex justify-end">
        <flux:button
            variant="primary"
            icon="plus"
            class="!bg-blue-800 hover:!bg-blue-900 !border-blue-800 !text-white"
            x-on:click="$flux.modal('modal-crear-taquilla').show()">
            Nueva Taquilla
        </flux:button>
    </div>

    @livewire('taquilla.info-taquillas')
    @livewire('taquilla.grid-taquillas')
    @livewire('taquilla.modal')
</div>
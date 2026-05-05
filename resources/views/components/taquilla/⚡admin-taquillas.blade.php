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
    @livewire('taquilla.info-taquillas')
    @livewire('taquilla.grid-taquillas')
</div>
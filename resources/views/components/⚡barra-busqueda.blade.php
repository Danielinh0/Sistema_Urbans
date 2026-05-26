<?php

use Livewire\Component;

new class extends Component {
    public $filters = [];
    public $placeholder = 'Buscar por nombre de ruta';
    public $search_var = '';
    public $filter = '';
    public $filtrosSeleccionados = [];

    public function updatedFiltrosSeleccionados()
    {
        $this->dispatch('filterUpdated', filters: $this->filtrosSeleccionados);
    }
};
?>


<div class="flex flex-col sm:flex-row sm:items-center gap-3 mb-4">

    <flux:input
        icon="magnifying-glass"
        placeholder="{{ $this->placeholder }}"
        wire:model.blur="search_var"
        wire:change="$parent.updateSearch($event.target.value)"
        class="w-full" />

    @foreach ($filters as $name => $config)
    <flux:select
        size="md"
        class="w-full sm:w-48"
        wire:model.live="filtrosSeleccionados.{{ $name }}">

        <flux:select.option value="">{{ $config['label'] }}</flux:select.option>

        @foreach ($config['options'] as $value => $label)
        <flux:select.option :value="$value">{{ $label }}</flux:select.option>
        @endforeach

    </flux:select>
    @endforeach

</div>
<?php

use Livewire\Component;

new class extends Component {
    public $filters = [];
    public $placeholder = 'Buscar por nombre de ruta';
    public $search_var = '';
    public $filter = '';
    public $filtrosSeleccionados = [];

    public function updatedFiltrosSeleccionados($value, $key)
    {
        $this->dispatch('filterUpdated', name: $key, value: $value);
    }

};
?>


<div class="flex flex-row items-center gap-4 mb-4 ">
    <flux:input icon="magnifying-glass" placeholder="{{ $this->placeholder }}" wire:model.blur="search_var"
        wire:change="$parent.updateSearch($event.target.value)" />
    @foreach ($filters as $name => $config)
        <flux:select size="md" class="w-48" wire:model.live="filtrosSeleccionados.{{ $name }}">

            <flux:select.option value="">{{ $config['label'] }}</flux:select.option>

            @foreach ($config['options'] as $value => $label)
                <flux:select.option :value="$value">{{ $label }}</flux:select.option>
            @endforeach
        </flux:select>
    @endforeach
</div>
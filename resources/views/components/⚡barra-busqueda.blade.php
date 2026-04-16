<?php

use Livewire\Component;

new class extends Component
{
    public $filters = [];
    public $placeholder = 'Buscar por nombre de ruta';
    public $search_var = '';
    public $filter = '';


    public function updatedFilter($value)
    {
        $this->dispatch('filterUpdated', value: $value);
    }
    
};
?>

<div>
    <div class = "flex flex-row items-center gap-4 mb-4 ">
        <flux:input 
            icon="magnifying-glass" 
            placeholder="{{ $this->placeholder }}" 
            wire:model.blur="search_var"
            wire:change="$parent.updateSearch($event.target.value)"
        />
        @if ($this->filters && count($this->filters) > 0)  
            <flux:select 
                size="md" 
                class="w-full sm:w-auto" 
                wire:model.live="filter"
            >
                @forelse ($this->filters as $key => $label)
                    <flux:select.option :value="$key">{{$label}}</flux:select.option>
                @empty
                    <flux:select.option value="">No hay filtros disponibles</flux:select.option>
                @endforelse
                
            </flux:select>
        @endif
    </div>
</div>
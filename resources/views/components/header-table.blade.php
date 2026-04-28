@props([
    'icon' => null,
    'sortable' => null,
    'sortBy' => null,
    'sortDirection' => null,
])

@if($sortable)
    <flux:table.column 
        sortable 
        :sorted="$sortBy === $sortable" 
        :direction="$sortDirection" 
        wire:click="sort('{{ $sortable }}')"
        {{ $attributes }}
    >
        <span class="inline-flex items-center gap-1 whitespace-nowrap text-azul_menu text-sm font-semibold">
            @if($icon)
                <flux:icon :name="$icon" class="text-azul_menu! size-4" />
            @endif

            {{ $slot }}
        </span>
    </flux:table.column>
@else
    <flux:table.column {{ $attributes }}>
        <span class="inline-flex items-center gap-1 whitespace-nowrap text-azul_menu text-sm font-semibold">
            @if($icon)
                <flux:icon :name="$icon" class="text-azul_menu! size-4" />
            @endif

            {{ $slot }}
        </span>
    </flux:table.column>
@endif
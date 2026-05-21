@props([
    'icon' => null,
    'sortable' => null,
    'sortBy' => null,
    'sortDirection' => null,
    'width' => 'auto',
])

@php
    $widthStyle = $width !== 'auto' ? 'width: ' . $width . ';' : null;
@endphp

@if($sortable)
    <flux:table.column 
        sortable 
        :sorted="$sortBy === $sortable" 
        :direction="$sortDirection" 
        wire:click="sort('{{ $sortable }}')"
        style="{{ $widthStyle }}"
        {{ $attributes }}
    >
        <span class="inline-flex items-center gap-1 whitespace-nowrap text-azul_menu text-sm font-semibold">
            @if($icon)
                <flux:icon :name="$icon" class="text-azul_menu! " />
            @endif

            {{ $slot }}
        </span>
    </flux:table.column>
@else
    <flux:table.column style="{{ $widthStyle }}" {{ $attributes }}>
        <span class="inline-flex items-center gap-1 whitespace-nowrap text-azul_menu text-sm font-semibold">
            @if($icon)
                <flux:icon :name="$icon" class="text-azul_menu!" />
            @endif

            {{ $slot }}
        </span>
    </flux:table.column>
@endif
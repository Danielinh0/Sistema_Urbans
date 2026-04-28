
@props([
    'texto' => '',
    'fuerte' => false,
])

@if($fuerte)
        <flux:badge class="bg-azul_menu! text-white!">
            {{ $texto }}
        </flux:badge>

@else
    <flux:badge class="bg-azul_rebajado! text-azul_menu!">
        {{ $texto }}
    </flux:badge>
@endif
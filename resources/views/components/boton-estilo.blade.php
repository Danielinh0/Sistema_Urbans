@props([
    'bg' => 'bg-azul_boton',
    'c_text' => '',
    'icon' => '',
    'text' => '',
    'evento' => ''
])

<div>
    <flux:button type="button" class="!{{ $bg }} !{{ $c_text }} 
                 transition delay-150 duration-300 ease-in-out hover:-translate-y-1 hover:scale-105
                 hover:{{ $bg }}/110" icon="{{ $icon }}" wire:click="{{ $evento }}">
                 {{ $text }}
    </flux:button>
</div>


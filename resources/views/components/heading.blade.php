@props([
    'icono' => null, 
    'texto' => null
])

<div class ="flex items-center gap-3 ">
    <flux:icon :name="$icono" class="inline size-9 text-azul_menu" />
    <flux:text class="text-2xl xs:text-3xl font-bold !text-azul_menu ">{{ $texto }}</flux:text>
</div>


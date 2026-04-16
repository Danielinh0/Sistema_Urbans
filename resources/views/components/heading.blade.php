@props([
    'icono' => null, 
    'texto' => null
])

<div class ="flex items-center gap-4 ">
    <flux:icon :name="$icono" class="inline size-12 text-azul_menu" />
    <flux:text class="text-3xl xs:text-4xl font-bold !text-azul_menu ">{{ $texto }}</flux:text>
</div>


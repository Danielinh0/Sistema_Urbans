@props([
    'icono' => null,
    'fondo_icono' => null,
    'color_icono' => null,
    'contador' => null,
    'texto' => null
])
<div class= "flex gap-6 rounded-xl items-center shadow-md p-7 bg-white">
                
    <div class= "rounded-full {{ $fondo_icono }} p-2 flex items-center gap-2">
        <flux:icon name="{{ $icono }}" class="{{ $color_icono }} size-10!" /> 
    </div>

    <div>
        <flux:text class="text-3xl font-bold text-[#404040]">{{ $contador }}</flux:text>
        <flux:text class="text-xl text-[#808080]">{{ $texto }}</flux:text>
    </div>
</div>
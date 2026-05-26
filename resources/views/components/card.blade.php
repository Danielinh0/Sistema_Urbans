@props([
'icono' => null,
'fondo_icono' => null,
'color_icono' => null,
'contador' => null,
'texto' => null
])

{{-- Mobile  : flex-col, compacto (cabe en 2 columnas)
     Desktop : flex-row, espacioso (diseño original) --}}
<div class="flex flex-col sm:flex-row gap-3 sm:gap-6
            rounded-2xl items-center sm:items-center
            border border-neutral-200 dark:border-neutral-700
            shadow-sm p-4 sm:p-6
            bg-white dark:bg-neutral-900
            w-full text-center sm:text-left
            transition duration-300 ease-in-out
            hover:-translate-y-1.5 sm:hover:-translate-y-3
            cursor-pointer">

    {{-- Ícono --}}
    <div class="rounded-full p-2.5 sm:p-3 {{ $fondo_icono }} flex items-center justify-center shrink-0">
        <flux:icon name="{{ $icono }}" class="{{ $color_icono }} size-7! sm:size-9!" />
    </div>

    {{-- Texto --}}
    <div class="flex flex-col items-center sm:items-start">
        <flux:text class="text-2xl sm:text-3xl font-bold text-[#404040] dark:text-white leading-tight">
            {{ $contador }}
        </flux:text>
        <flux:text class="text-xs sm:text-base text-[#808080] dark:text-neutral-400 leading-snug">
            {{ $texto }}
        </flux:text>
    </div>
</div>
@props([
    'wire' => '',
    'texto' => 'Hora de llegada',
])

<div>
    <flux:field>
        <div class="space-y-2">
            <label for="{{ $wire }}" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                {{ $texto }} <flux:badge class="!text-zinc-800/70 !ml-0.5" size="sm" color="zinc" inset="top bottom">Obligatorio</flux:badge>
            </label>
            
            <input
            type="time" id="{{ $wire }}" name="{{ $wire }}" wire:model="{{ $wire }}" step="60" required
            class="w-full border rounded-lg block disabled:shadow-none dark:shadow-none appearance-none text-base sm:text-sm py-2 h-10 leading-[1.375rem] ps-3 pe-3 bg-white dark:bg-white/10 dark:disabled:bg-white/[7%] text-zinc-700 disabled:text-zinc-500 placeholder-zinc-400 disabled:placeholder-zinc-400/70 dark:text-zinc-300 dark:disabled:text-zinc-400 dark:placeholder-zinc-400 dark:disabled:placeholder-zinc-500 shadow-xs border-zinc-200 border-b-zinc-300/80 disabled:border-b-zinc-200 dark:border-white/10 dark:disabled:border-white/5 data-invalid:shadow-none data-invalid:border-red-500 dark:data-invalid:border-red-500 disabled:data-invalid:border-red-500 dark:disabled:data-invalid:border-red-500" 
            >
        </div>
         <flux:error name="{{ $wire }}" />
    </flux:field>
</div>
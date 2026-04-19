@props([
    'icon' => '',
    'ruta' => '',
    'texto' => '',
])

<flux:sidebar.item
    class="h-8.5 border border-transparent transition duration-300 ease-in-out
           hover:bg-azul_rebajado! hover:translate-x-2.5 hover:!text-azul_menu
           dark:hover:bg-white/7! dark:hover:!text-white
           data-current:bg-azul_menu! data-current:!text-white data-current:border-zinc-200!
           hover:data-current:!text-white
           [&_[data-flux-icon]]:!size-5"
    :icon="$icon"
    :href="route($ruta)"
    :current="request()->routeIs($ruta)"
    wire:navigate
>
    {{ __($texto) }}
</flux:sidebar.item>
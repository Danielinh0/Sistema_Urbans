<form wire:submit="save" class="px-4 py-4">
    <flux:card>
        <x-skeleton-form-ruta />

        <div class="mt-5">
            <flux:button
                icon="map-pinned"
                type="submit"
                variant="primary"
                class="w-full bg-azul_rebajado! text-azul_menu!
                     hover:bg-azul_menu! hover:text-white! hover:-translate-y-1/4
                       transition delay-130 duration-300 ease-in-out cursor-pointer border-none!"
                :disabled="!$this->formularioListo">
                Crear Ruta
            </flux:button>
        </div>
    </flux:card>
</form>
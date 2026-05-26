<x-layouts::app :title="__('Sucursal')">

    <main class="flex flex-col gap-6 px-9 pt-2">

        <header class="flex flex-col justify-between items-center md:flex-row">
            <div>
                <x-heading :icono="'building-2'" texto="Sucursales" />
            </div>

            <div class="pr-2">
                @can('create', App\Models\Sucursal::class)
                <flux:modal.trigger name="Sucursal-form">
                    <flux:button class="bg-azul_rebajado! cursor-pointer text-azul_menu! hover:bg-azul_menu! hover:text-white!
                    transition delay-150 duration-300 ease-in-out hover:-translate-y-1/4 hover:scale-110"
                        icon="house-plus"> Nueva sucursal </flux:button>
                </flux:modal.trigger>
                @endcan
            </div>
        </header>

        <section class="bg-white dark:bg-neutral-900
                rounded-2xl border border-neutral-200 dark:border-neutral-700
                p-4 sm:p-6 space-y-4">

            <div class="flex items-center gap-2.5 px-1">
                <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-blue-100 dark:bg-blue-900/40 shrink-0">
                    <flux:icon name="building-2" class="size-4 text-azul_menu" />
                </span>
                <flux:text class="text-base font-bold text-azul_menu">
                    Estado de la infraestructura
                </flux:text>
            </div>

            <div class="grid grid-cols-2 xl:grid-cols-4 gap-3 sm:gap-4">

                <x-card
                    icono="building-2"
                    fondo_icono="bg-[#ccf7f2]!"
                    color_icono="text-[#005f5a]!"
                    contador="{{ $totalSucursales }}"
                    texto="Sucursales registradas" />

                <x-card
                    icono="map-pin-house"
                    fondo_icono="bg-[#fcebdb]!"
                    color_icono="text-[#f39c12]!"
                    contador="{{ $sucursalesSinRutasSalida }}"
                    texto="Sin rutas de salida" />

                <x-card
                    icono="layout-grid"
                    fondo_icono="bg-[#ccf6fc]!"
                    color_icono="text-[#005f78]!"
                    contador="{{ $sucursalesCompartidas }}"
                    texto="Inmuebles compartidos" />

                <x-card
                    icono="map-pin-off"
                    fondo_icono="bg-[#f1e1f7]!"
                    color_icono="text-[#bb6bd9]!"
                    contador="{{ $sucursalesAisladas }}"
                    texto="Terminales sin rutas" />

            </div>
        </section>

        <div>
            <livewire:sucursal.table />
        </div>

        <flux:modal name="Sucursal-form" class="w-8/10 xl:w-[60%] xl:p-10">
            <div>
                <flux:heading class="!text-xl !font-bold" size="lg">Crea una nueva sucursal</flux:heading>
            </div>
            <livewire:sucursal.form />
        </flux:modal>

    </main>

</x-layouts::app>
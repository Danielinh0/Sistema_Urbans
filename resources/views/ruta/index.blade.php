<x-layouts::app :title="__('Rutas')">

    <main class="flex flex-col gap-6 px-6 pt-2">

        <header class="flex flex-col justify-between items-center md:flex-row">
            <div>
                <x-heading :icono="'map-pin-house'" texto="Rutas de viaje" />
            </div>

            @if(auth()->user()->hasAnyRole(['gerente', 'admin']))
            <div class="pr-2">
                <flux:modal.trigger name="crear-ruta">
                    <flux:button class="
                     bg-azul_rebajado! text-azul_menu!
                     hover:bg-azul_menu! hover:text-white! hover:-translate-y-1/4 hover:scale-110
                     transition delay-150 duration-300 ease-in-out cursor-pointer border-none!"
                        icon="map-pin-plus"> Nueva ruta </flux:button>
                </flux:modal.trigger>
            </div>
            @endif
        </header>

        <section class="bg-white dark:bg-neutral-900
                rounded-2xl border border-neutral-200 dark:border-neutral-700
                p-4 sm:p-6 space-y-4">

            <div class="flex items-center gap-2.5 px-1">
                <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-blue-100 dark:bg-blue-900/40 shrink-0">
                    <flux:icon name="chart-bar" class="size-4 text-azul_menu" />
                </span>
                <flux:text class="text-base font-bold text-azul_menu">
                    Análisis y estados de rutas
                </flux:text>
            </div>

            <div class="grid grid-cols-2 xl:grid-cols-4 gap-3 sm:gap-4">

                <x-card
                    icono="trending-up"
                    fondo_icono="bg-[#ccf7f2]!"
                    color_icono="text-[#005f5a]!"
                    contador="{{ $rutasAltaDemanda }}"
                    texto="Rutas con alta demanda" />

                <x-card
                    icono="alert-circle"
                    fondo_icono="bg-[#fcebdb]!"
                    color_icono="text-[#f39c12]!"
                    contador="{{ $rutasSinAsignacion }}"
                    texto="Rutas sin asignación" />

                <x-card
                    icono="clock"
                    fondo_icono="bg-[#ccf6fc]!"
                    color_icono="text-[#005f78]!"
                    contador="{{ $rutasMayorDuracion }}"
                    texto="Rutas de larga duración" />

                <x-card
                    icono="dollar-sign"
                    fondo_icono="bg-[#f1e1f7]!"
                    color_icono="text-[#bb6bd9]!"
                    contador="{{ $rutasMasCaras }}"
                    texto="Rutas con tarifa premium" />

            </div>
        </section>

        <div class="px-3">
            <livewire:rutas.tabla />
        </div>

    </main>

    <div>
        <flux:modal name="crear-ruta" class="w-8/10 xl:w-[60%] xl:p-6" x-on:close="Livewire.dispatch('reset-form')">
            <div class="pl-4">
                <flux:heading class="!text-2xl !font-bold">Crea una nueva ruta</flux:heading>
            </div>
            <livewire:rutas.form />
        </flux:modal>

        <flux:modal name="rutas-alta-demanda" class="!max-w-4xl p-9">
            <div class="flex flex-col gap-3">
                <div class="flex items-center gap-3 p-3">
                    <flux:icon name="trending-up" class="inline size-9 text-azul_menu" />
                    <flux:text class="text-sm xs:text-3xl font-medium !text-azul_menu">Rutas más solicitadas hoy</flux:text>
                </div>
                <flux:text>Listado detallado de rutas con alta demanda.</flux:text>
            </div>
        </flux:modal>

        <flux:modal name="rutas-sin-asignacion" class="!max-w-4xl p-9">
            <div class="flex flex-col gap-3">
                <div class="flex items-center gap-3 p-3">
                    <flux:icon name="alert-circle" class="inline size-9 text-azul_menu" />
                    <flux:text class="text-sm xs:text-3xl font-medium !text-azul_menu">Rutas sin viajes hoy</flux:text>
                </div>
                <flux:text>Listado de rutas que requieren atención u optimización de horarios.</flux:text>
            </div>
        </flux:modal>
    </div>

</x-layouts::app>
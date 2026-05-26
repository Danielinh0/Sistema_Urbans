<x-layouts::app :title="__('Corridas')">

    <main class="flex flex-col gap-3 px-4 pt-2">
        <header class="flex flex-col justify-between items-center md:flex-row">
            <div>
                <x-heading :icono="'map'" texto="Corridas" />
            </div>

            @if(auth()->user()->hasAnyRole(['gerente', 'admin']))
            <div>
                <flux:modal.trigger name="crear-corrida">
                    <flux:button icon="map-plus"
                        class="bg-azul_rebajado! cursor-pointer text-azul_menu! hover:bg-azul_menu!
                    hover:text-white! transition delay-150 duration-300 ease-in-out hover:-translate-y-1/4 hover:scale-110">Crear
                        una nueva corrida</flux:button>
                </flux:modal.trigger>
            </div>
            @endif


        </header>

        <section class="bg-white dark:bg-neutral-900
                rounded-2xl border border-neutral-200 dark:border-neutral-700
                p-4 sm:p-6 space-y-4">

            <div class="flex items-center gap-2.5 px-1">
                <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-blue-100 dark:bg-blue-900/40 shrink-0">
                    <flux:icon name="sunrise" class="size-4 text-azul_menu" />
                </span>
                <flux:text class="text-base font-bold text-azul_menu">
                    Información del día
                </flux:text>
            </div>

            <div class="grid grid-cols-2 xl:grid-cols-4 gap-3 sm:gap-4">

                <flux:modal.trigger name="corridas-programadas">
                    <x-card
                        icono="calendar-clock"
                        fondo_icono="bg-[#ccf7f2]!"
                        color_icono="text-[#005f5a]!"
                        contador="{{ $corridasEnProceso }}"
                        texto="Corridas programadas" />
                </flux:modal.trigger>

                <flux:modal.trigger name="corridas-viaje">
                    <x-card
                        icono="luggage"
                        fondo_icono="bg-[#fcebdb]!"
                        color_icono="text-[#f39c12]!"
                        contador="{{ $corridasEnViaje }}"
                        texto="Corridas en viaje" />
                </flux:modal.trigger>

                <x-card
                    icono="van"
                    fondo_icono="bg-[#ccf6fc]!"
                    color_icono="text-[#005f78]!"
                    contador="{{ $urbansOcupadas }}"
                    texto="Urbans ocupadas" />

                <x-card
                    icono="key-square"
                    fondo_icono="bg-[#f1e1f7]!"
                    color_icono="text-[#bb6bd9]!"
                    contador="{{ $choferesOcupados }}"
                    texto="Choferes ocupados" />

            </div>
        </section>

        <section>
            <livewire:corrida.tabla />
        </section>

    </main>

    <div>
        <flux:modal name="crear-corrida" class="w-[50%] p-10">
            <livewire:corrida.form />
        </flux:modal>

        <flux:modal name="corridas-programadas" class="!max-w-4xl p-9">
            <div class="flex flex-col gap-3">
                <div class="flex items-center gap-3 p-3">
                    <flux:icon name="map" class="inline size-9 text-azul_menu" />
                    <flux:text class="text-sm xs:text-3xl font-medium !text-azul_menu ">Corridas programadas</flux:text>
                </div>
                <livewire:corrida.card.table />
            </div>
        </flux:modal>

        <flux:modal name="corridas-viaje" class="!max-w-4xl p-9">
            <div class="flex flex-col gap-3">
                <div class="flex items-center gap-3 p-3">
                    <flux:icon name="tickets-plane" class="inline size-9 text-azul_menu" />
                    <flux:text class="text-sm xs:text-3xl font-medium !text-azul_menu ">Corridas en viaje</flux:text>
                </div>
                <livewire:corrida.card.table-viaje />
            </div>
        </flux:modal>
    </div>

    <!-- Tu modal para editar/eliminar que usarán TODAS las tablas 1 sola vez en el DOM -->
    <livewire:corrida.modal />

</x-layouts::app>
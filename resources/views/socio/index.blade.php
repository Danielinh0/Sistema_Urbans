<x-layouts::app :title="__('Socio')">
    <main class="flex flex-col gap-6 px-9 pt-2">

        <header class="flex flex-col justify-between items-center md:flex-row">
            <div>
                <x-heading :icono="'user'" texto="Socios" />
            </div>

            @if(auth()->user()->hasAnyRole(['gerente', 'admin']))
            <div>
                <flux:modal.trigger name="edit-socio">
                    <flux:button icon="user-plus" class="bg-azul_rebajado! cursor-pointer text-azul_menu! hover:bg-azul_menu! hover:text-white!
                    transition delay-150 duration-300 ease-in-out hover:-translate-y-1/4 hover:scale-110">Crea un nuevo socio</flux:button>
                </flux:modal.trigger>
            </div>
            @endif
        </header>

        <section class="bg-white dark:bg-neutral-900
                rounded-2xl border border-neutral-200 dark:border-neutral-700
                p-4 sm:p-6 space-y-4">

            <div class="flex items-center gap-2.5 px-1">
                <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-blue-100 dark:bg-blue-900/40 shrink-0">
                    <flux:icon name="book-user" class="size-4 text-azul_menu" />
                </span>
                <flux:text class="text-base font-bold text-azul_menu">
                    Estado de los socios
                </flux:text>
            </div>

            <div class="grid grid-cols-2 xl:grid-cols-4 gap-3 sm:gap-4">

                <x-card
                    icono="book-user"
                    fondo_icono="bg-[#ccf7f2]!"
                    color_icono="text-[#005f5a]!"
                    contador="{{ $sociosActivos }}"
                    texto="Socios activos" />

                <x-card
                    icono="bus"
                    fondo_icono="bg-[#fcebdb]!"
                    color_icono="text-[#f39c12]!"
                    contador="{{ $sociosSinFlotilla }}"
                    texto="Socios sin flotilla" />

                <x-card
                    icono="user-round-plus"
                    fondo_icono="bg-[#ccf6fc]!"
                    color_icono="text-[#005f78]!"
                    contador="{{ $nuevosSociosMes }}"
                    texto="Nuevas incorporaciones" />

                <x-card
                    icono="user-round-minus"
                    fondo_icono="bg-[#f1e1f7]!"
                    color_icono="text-[#bb6bd9]!"
                    contador="{{ $sociosEliminados }}"
                    texto="Socios inactivos" />

            </div>
        </section>

        <div>
            <livewire:socio.tabla />
        </div>
    </main>

    <flux:modal name="edit-socio" class="w-[50%] p-10" x-on:close="Livewire.dispatch('reset-form')">
        <div>
            <flux:heading class="!text-xl !font-bold" size="lg">Crea un nuevo socio</flux:heading>
        </div>
        <livewire:socio.form />
    </flux:modal>
</x-layouts::app>
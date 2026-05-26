<x-layouts::app :title="__('Usuarios')">

    <main class="flex flex-col gap-6 px-9 pt-2">

        <header class="flex flex-col justify-between items-center md:flex-row">
            <div>
                <x-heading :icono="'users'" texto="Usuarios" />
            </div>

            @if(auth()->user()->hasAnyRole(['gerente', 'admin']))
            <div>
                <flux:modal.trigger name="add-usuario">
                    <flux:button icon="squares-plus"
                        class="bg-azul_rebajado! cursor-pointer text-azul_menu! hover:bg-azul_menu!
                        hover:text-white! transition delay-150 duration-300 ease-in-out hover:-translate-y-1/4 hover:scale-110">
                        Añadir usuario
                    </flux:button>
                </flux:modal.trigger>
            </div>
            @endif
        </header>

        <section class="bg-white dark:bg-neutral-900
                rounded-2xl border border-neutral-200 dark:border-neutral-700
                p-4 sm:p-6 space-y-4">

            <div class="flex items-center gap-2.5 px-1">
                <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-blue-100 dark:bg-blue-900/40 shrink-0">
                    <flux:icon name="user-round-plus" class="size-4 text-azul_menu" />
                </span>
                <flux:text class="text-base font-bold text-azul_menu">
                    Control y estado del personal
                </flux:text>
            </div>

            <div class="grid grid-cols-2 xl:grid-cols-4 gap-3 sm:gap-4">

                <x-card
                    icono="clock-fading"
                    fondo_icono="bg-[#ccf7f2]!"
                    color_icono="text-[#005f5a]!"
                    contador="{{ $usuariosEnTurno }}"
                    texto="Cajeros en turno" />

                <x-card
                    icono="key-square"
                    fondo_icono="bg-[#fcebdb]!"
                    color_icono="text-[#f39c12]!"
                    contador="{{ $administradoresYGerentes }}"
                    texto="Cuentas administrativas" />

                <x-card
                    icono="alert-circle"
                    fondo_icono="bg-[#ccf6fc]!"
                    color_icono="text-[#005f78]!"
                    contador="{{ $usuariosSinTurno }}"
                    texto="Cajeros fuera de turno" />

                <x-card
                    icono="user-round-plus"
                    fondo_icono="bg-[#f1e1f7]!"
                    color_icono="text-[#bb6bd9]!"
                    contador="{{ $conductoresHoy }}"
                    texto="Conductores en ruta hoy" />

            </div>
        </section>

        <div>
            <livewire:usuario.table />
        </div>

        <flux:modal name="add-usuario" class="w-[50%] p-10">
            <div>
                <flux:heading class="!text-xl !font-bold" size="lg">Añadir nuevo usuario</flux:heading>
            </div>
            <livewire:usuario.form />
        </flux:modal>

    </main>

</x-layouts::app>
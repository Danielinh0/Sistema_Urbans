<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    @include('partials.head')
</head>

<body class="min-h-screen bg-white dark:bg-zinc-800">
    <flux:sidebar sticky collapsible="mobile"
        class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
        <flux:sidebar.header>

            <x-app-logo :sidebar="true" href="{{ route('dashboard') }}" wire:navigate />

            <flux:sidebar.collapse class="lg:hidden" />

        </flux:sidebar.header>

        <flux:sidebar.nav>

            <flux:sidebar.group :heading="__('Menu principal')" class="grid gap-4">
                <x-item-sidebar icon="home" ruta="dashboard" texto="Dashboard" />

                @if(auth()->user()->hasAnyRole(['admin', 'gerente']))
                <x-item-sidebar icon="map-pinned" ruta="ruta.index" texto="Rutas" :disabled="!$hayTurnoActivo" />
                @endif

                @if(auth()->user()->hasAnyRole(['admin', 'gerente']))
                <x-item-sidebar icon="user-round" ruta="socio.index" texto="Socios" />
                <x-item-sidebar icon="bus" ruta="urban.index" texto="Urbans" />
                @endif
                <x-item-sidebar icon="map" ruta="corrida.index" texto="Corridas" :disabled="!$hayTurnoActivo" />
                <x-item-sidebar icon="building-2" ruta="sucursal.index" texto="Sucursales" :disabled="!$hayTurnoActivo" />
                <x-item-sidebar icon="users" ruta="cliente.index" texto="Clientes" :disabled="!$hayTurnoActivo" />
                @if(auth()->user()->hasAnyRole(['admin', 'gerente']))
                <x-item-sidebar icon="activity" ruta="prediccion.index" texto="Predicción" />
                @endif

                @if(auth()->user()->hasRole('admin'))
                <x-item-sidebar icon="users" ruta="usuario.index" texto="Usuarios" />
                @endif




            </flux:sidebar.group>

        </flux:sidebar.nav>

        <flux:spacer />



        <x-desktop-user-menu class="hidden lg:block" :name="auth()->user()->name" />
    </flux:sidebar>

    <!-- Mobile User Menu -->
    <flux:header class="lg:hidden">
        <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

        <flux:spacer />

        <flux:dropdown position="top" align="end">
            <flux:profile :initials="auth()->user()->initials()" icon-trailing="chevron-down" />

            <flux:menu>
                <flux:menu.radio.group>
                    <div class="p-0 text-sm font-normal">
                        <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                            <flux:avatar :name="auth()->user()->name" :initials="auth()->user()->initials()" />

                            <div class="grid flex-1 text-start text-sm leading-tight">
                                <flux:heading class="truncate">{{ auth()->user()->name }}</flux:heading>
                                <flux:text class="truncate">{{ auth()->user()->email }}</flux:text>
                            </div>
                        </div>
                    </div>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <flux:menu.radio.group>
                    <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>
                        {{ __('Settings') }}
                    </flux:menu.item>
                </flux:menu.radio.group>

                @if(auth()->user()->hasRole('cajero'))
                <flux:menu.separator />
                <flux:menu.radio.group>
                    @if($hayTurnoActivo)
                    <form method="POST" action="{{ route('turno.close') }}" class="w-full">
                        @csrf
                        <flux:menu.item
                            as="button"
                            type="submit"
                            icon="alarm-clock"
                            class="w-full cursor-pointer text-red-500 dark:text-red-400">
                            {{ __('Cerrar turno') }}
                        </flux:menu.item>
                    </form>
                    @else
                    <flux:menu.item
                        icon="alarm-clock"
                        disabled
                        class="w-full opacity-50 cursor-not-allowed text-zinc-400">
                        {{ __('Cerrar turno') }}
                    </flux:menu.item>
                    @endif
                </flux:menu.radio.group>
                @endif

                <flux:menu.separator />

                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle"
                        class="w-full cursor-pointer" data-test="logout-button">
                        {{ __('Log out') }}
                    </flux:menu.item>
                </form>
            </flux:menu>
        </flux:dropdown>
    </flux:header>

    {{ $slot }}

    @fluxScripts

    @persist('toast')
    <flux:toast />
    @endpersist
</body>

</html>